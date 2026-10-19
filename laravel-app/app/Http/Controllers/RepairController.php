<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Repair;
use App\Models\User;
use App\Services\MailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RepairController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');
        $query = Repair::query()->orderBy('created_at', 'desc');

        if ($role === 'technician') {
            $techName = $request->input('tech', 'Alex Kim');
            $query->where('technician_name', $techName);
        } elseif ($role === 'customer') {
            $user = Auth::user();
            if ($user && $user->role === 'customer') {
                $query->where(function ($q) use ($user) {
                    $q->where('customer_id', $user->id)
                      ->orWhere('customer_email', $user->email);
                });
            }
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('id', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('device', 'like', "%{$search}%")
                  ->orWhere('serial', 'like', "%{$search}%")
                  ->orWhere('issue', 'like', "%{$search}%");
            });
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'serial' => 'required|string|max:100',
            'symptom' => 'required|string',
            'delivery' => 'required|in:mail,dropoff',
            'shipping_slip' => 'required_if:delivery,mail',
        ]);

        $user = Auth::user() ?: User::where('role', 'customer')->first();

        // Handle up to 5 symptom photos
        $devicePhotoPaths = [];
        if ($request->hasFile('device_photos')) {
            $files = $request->file('device_photos');
            if (!is_array($files)) {
                $files = [$files];
            }
            foreach (array_slice($files, 0, 5) as $photoFile) {
                if ($photoFile && $photoFile->isValid()) {
                    $path = $photoFile->store('repairs/devices', 'public');
                    $devicePhotoPaths[] = '/storage/' . $path;
                }
            }
        } elseif ($request->hasFile('device_photo')) {
            $path = $request->file('device_photo')->store('repairs/devices', 'public');
            $devicePhotoPaths[] = '/storage/' . $path;
        }

        $shippingSlipPath = null;
        if ($request->hasFile('shipping_slip')) {
            $path = $request->file('shipping_slip')->store('repairs/slips', 'public');
            $shippingSlipPath = '/storage/' . $path;
        }

        // Infer device name from symptom or serial or fallback
        $device = $request->input('device') ?: 'PC Workstation (' . mb_substr($request->serial, 0, 8) . ')';
        $ticketId = Repair::generateId();

        $method = $request->delivery === 'mail' ? 'Mail-in' : 'Drop-off';
        $status = 'Pending';

        $repair = Repair::create([
            'id' => $ticketId,
            'customer_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'device' => $device,
            'serial' => $request->serial,
            'issue' => $request->symptom,
            'method' => $method,
            'status' => $status,
            'technician_id' => null,
            'technician_name' => 'Unassigned',
            'active_step' => 0,
            'latest_update' => $method === 'Mail-in'
                ? "Repair request registered with shipping slip. Awaiting parcel intake inspection."
                : "Request created. Ready for workshop review and diagnostic assignment.",
            'device_photo' => !empty($devicePhotoPaths) ? $devicePhotoPaths[0] : null,
            'device_photos' => $devicePhotoPaths,
            'shipping_slip' => $shippingSlipPath,
            'slip_verified' => false,
            'interacted' => false,
            'created_date_formatted' => date('M d, Y'),
        ]);

        // Create Activity Center notifications
        Notification::create([
            'role' => 'customer',
            'user_id' => $user->id,
            'icon' => 'tool',
            'title' => "Repair request confirmed {$repair->id}",
            'message' => "Your request for {$repair->device} was received by the workshop.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'repairs',
            'entity_id' => $repair->id,
            'is_read' => false,
        ]);

        Notification::create([
            'role' => 'admin',
            'user_id' => null,
            'icon' => 'inbox',
            'title' => "New {$repair->method} repair request {$repair->id}",
            'message' => "{$repair->customer_name} submitted a repair ticket for {$repair->device}.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'requests',
            'entity_id' => $repair->id,
            'is_read' => false,
        ]);

        // Send Email Notification via SMTP
        MailNotificationService::notifyRepairCreated($repair);

        return response()->json([
            'success' => true,
            'repair' => $repair,
            'message' => "Repair ticket {$repair->id} submitted successfully.",
        ]);
    }

    public function updateStatus(Request $request, string $id)
    {
        $repair = Repair::findOrFail($id);

        $request->validate([
            'status' => 'required|string',
            'note' => 'nullable|string',
        ]);

        $oldStatus = $repair->status;
        $newStatus = $request->status;
        $note = $request->note;

        // Calculate active step
        if ($repair->method === 'Mail-in') {
            $statusMap = [
                'Package received' => 0,
                'Pending' => 0,
                'Diagnosing' => 1,
                'Waiting for part' => 2,
                'Fixing' => 3,
                'Finished' => 4,
            ];
        } else {
            $statusMap = [
                'Pending' => 0,
                'Diagnosing' => 0,
                'Waiting for part' => 1,
                'Fixing' => 2,
                'Finished' => 3,
            ];
        }

        $repair->status = $newStatus;
        if (isset($statusMap[$newStatus])) {
            $repair->active_step = $statusMap[$newStatus];
        }
        if (!empty($note)) {
            $repair->latest_update = $note;
        }
        $repair->interacted = true;
        $repair->save();

        // Activity center notification for customer
        Notification::create([
            'role' => 'customer',
            'user_id' => $repair->customer_id,
            'icon' => 'tool',
            'title' => "Status update: {$repair->id} is {$newStatus}",
            'message' => $note ?: "Your device is now marked as {$newStatus}.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'repairs',
            'entity_id' => $repair->id,
            'is_read' => false,
        ]);

        // Dispatch Email Notification via SMTP
        MailNotificationService::notifyStatusUpdated($repair, $oldStatus, $newStatus, $note);

        return response()->json([
            'success' => true,
            'repair' => $repair,
            'message' => 'Repair status updated and customer notified.',
        ]);
    }

    public function assignTechnician(Request $request, string $id)
    {
        $repair = Repair::findOrFail($id);
        $oldTechName = $repair->technician_name ?: 'Unassigned';
        $techName = $request->input('technician');

        if ($techName !== 'Unassigned' && !empty($techName)) {
            if ($repair->method === 'Mail-in' && $repair->status === 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot assign technician: Mail-in packages must be checked and marked as 'Package received' first.",
                ], 422);
            }
        }

        if ($techName === 'Unassigned' || empty($techName)) {
            $repair->technician_id = null;
            $repair->technician_name = 'Unassigned';
            $repair->save();

            // If there was an old technician, notify them that the job was unassigned
            if ($oldTechName !== 'Unassigned') {
                $oldTech = User::where('name', $oldTechName)->where('role', 'technician')->first();
                if ($oldTech) {
                    Notification::create([
                        'role' => 'technician',
                        'user_id' => $oldTech->id,
                        'icon' => 'tool',
                        'title' => "Repair {$repair->id} unassigned",
                        'message' => "{$repair->device} was unassigned from your queue.",
                        'upcoming' => false,
                        'time_label' => 'Just now',
                        'target' => 'assigned',
                        'entity_id' => $repair->id,
                        'is_read' => false,
                    ]);
                }
            }

            return response()->json(['success' => true, 'repair' => $repair]);
        }

        $technician = User::where('name', $techName)->where('role', 'technician')->first();
        $oldTech = ($oldTechName !== 'Unassigned' && $oldTechName !== $techName)
            ? User::where('name', $oldTechName)->where('role', 'technician')->first()
            : null;

        $repair->technician_id = $technician ? $technician->id : null;
        $repair->technician_name = $techName;
        $repair->save();

        if ($technician) {
            // 1. IN-WEB NOTIFICATION FOR NEW TECHNICIAN
            Notification::create([
                'role' => 'technician',
                'user_id' => $technician->id,
                'icon' => 'tool',
                'title' => "Assigned repair {$repair->id}",
                'message' => ($oldTechName !== 'Unassigned' && $oldTechName !== $techName)
                    ? "{$repair->device} was transferred to you from {$oldTechName}."
                    : "{$repair->device} was assigned to your active queue.",
                'upcoming' => false,
                'time_label' => 'Just now',
                'target' => 'assigned',
                'entity_id' => $repair->id,
                'is_read' => false,
            ]);

            // 2. EMAIL NOTIFICATION VIA SMTP TO NEW TECHNICIAN
            MailNotificationService::notifyRepairAssigned($repair, $technician, $oldTechName);

            // 3. IF TRANSFERRED FROM PREVIOUS TECH: NOTIFY PREVIOUS TECH VIA WEB & EMAIL
            if ($oldTech) {
                Notification::create([
                    'role' => 'technician',
                    'user_id' => $oldTech->id,
                    'icon' => 'tool',
                    'title' => "Repair {$repair->id} transferred",
                    'message' => "{$repair->device} was transferred from your queue to {$techName}.",
                    'upcoming' => false,
                    'time_label' => 'Just now',
                    'target' => 'assigned',
                    'entity_id' => $repair->id,
                    'is_read' => false,
                ]);

                MailNotificationService::notifyRepairTransferredAway($repair, $oldTech, $techName);
            }

            // 4. IN-WEB NOTIFICATION FOR CUSTOMER
            Notification::create([
                'role' => 'customer',
                'user_id' => $repair->customer_id,
                'icon' => 'tool',
                'title' => "Technician assigned to {$repair->id}",
                'message' => "{$techName} has been assigned to your repair.",
                'upcoming' => false,
                'time_label' => 'Just now',
                'target' => 'repairs',
                'entity_id' => $repair->id,
                'is_read' => false,
            ]);
        }

        return response()->json([
            'success' => true,
            'repair' => $repair,
            'message' => "Repair {$repair->id} moved to {$techName}. In-web and email notifications sent.",
        ]);
    }

    public function updateTeamWorkload(Request $request)
    {
        $request->validate([
            'job_id' => 'required|string',
            'technician' => 'nullable|string', // technician ID, name, slug, or null
            'technician_id' => 'nullable',
        ]);

        $repair = Repair::findOrFail($request->job_id);

        if ($repair->interacted) {
            return response()->json([
                'success' => false,
                'message' => 'This job has already started and is locked to its technician.',
            ], 422);
        }

        $oldTechName = $repair->technician_name ?: 'Unassigned';

        $targetTech = null;
        if ($request->technician && $request->technician !== 'unassigned' && $request->technician !== 'Unassigned') {
            $techInput = $request->technician;
            if ($request->filled('technician_id')) {
                $targetTech = User::where('id', $request->technician_id)->where('role', 'technician')->first();
            }
            if (!$targetTech && is_numeric($techInput)) {
                $targetTech = User::where('id', (int)$techInput)->where('role', 'technician')->first();
            }
            if (!$targetTech) {
                $targetTech = User::where('name', $techInput)->where('role', 'technician')->first();
            }
            if (!$targetTech) {
                $slugMap = [
                    'alex' => 'Alex Kim',
                    'sam' => 'Sam Rivera',
                    'priya' => 'Priya Shah',
                    'leo' => 'Leo Grant',
                ];
                if (isset($slugMap[$techInput])) {
                    $targetTech = User::where('name', $slugMap[$techInput])->where('role', 'technician')->first();
                } else {
                    foreach (User::where('role', 'technician')->get() as $u) {
                        if (\Illuminate\Support\Str::slug($u->name) === \Illuminate\Support\Str::slug($techInput)) {
                            $targetTech = $u;
                            break;
                        }
                    }
                }
            }
        }

        $targetTechName = $targetTech ? $targetTech->name : null;

        if ($targetTechName) {
            if ($repair->method === 'Mail-in' && $repair->status === 'Pending') {
                return response()->json([
                    'success' => false,
                    'message' => "Cannot assign technician: Mail-in packages must be checked and marked as 'Package received' first.",
                ], 422);
            }

            $tech = $targetTech;
            $oldTech = ($oldTechName !== 'Unassigned' && $oldTechName !== $targetTechName)
                ? User::where('name', $oldTechName)->where('role', 'technician')->first()
                : null;

            $repair->technician_id = $tech ? $tech->id : null;
            $repair->technician_name = $targetTechName;
            $repair->save();

            if ($tech) {
                // 1. IN-WEB NOTIFICATION TO NEW TECHNICIAN
                Notification::create([
                    'role' => 'technician',
                    'user_id' => $tech->id,
                    'icon' => 'tool',
                    'title' => "Repair {$repair->id} moved to your bench",
                    'message' => ($oldTechName !== 'Unassigned' && $oldTechName !== $targetTechName)
                        ? "Admin transferred {$repair->device} from {$oldTechName} to your queue."
                        : "Admin assigned {$repair->device} ({$repair->customer_name}) to your queue.",
                    'upcoming' => false,
                    'time_label' => 'Just now',
                    'target' => 'assigned',
                    'entity_id' => $repair->id,
                    'is_read' => false,
                ]);

                // 2. EMAIL NOTIFICATION VIA SMTP TO NEW TECHNICIAN
                MailNotificationService::notifyRepairAssigned($repair, $tech, $oldTechName);

                // 3. IF TRANSFERRED FROM PREVIOUS TECH: NOTIFY PREVIOUS TECH VIA WEB & EMAIL
                if ($oldTech) {
                    Notification::create([
                        'role' => 'technician',
                        'user_id' => $oldTech->id,
                        'icon' => 'tool',
                        'title' => "Repair {$repair->id} transferred",
                        'message' => "{$repair->device} was transferred from your queue to {$targetTechName}.",
                        'upcoming' => false,
                        'time_label' => 'Just now',
                        'target' => 'assigned',
                        'entity_id' => $repair->id,
                        'is_read' => false,
                    ]);

                    MailNotificationService::notifyRepairTransferredAway($repair, $oldTech, $targetTechName);
                }

                // 4. IN-WEB NOTIFICATION FOR CUSTOMER
                Notification::create([
                    'role' => 'customer',
                    'user_id' => $repair->customer_id,
                    'icon' => 'tool',
                    'title' => "Technician assigned to {$repair->id}",
                    'message' => "Your repair has been assigned to technician {$targetTechName}.",
                    'upcoming' => false,
                    'time_label' => 'Just now',
                    'target' => 'repairs',
                    'entity_id' => $repair->id,
                    'is_read' => false,
                ]);
            }
        } else {
            // Unassigning job
            if ($oldTechName !== 'Unassigned') {
                $oldTech = User::where('name', $oldTechName)->where('role', 'technician')->first();
                if ($oldTech) {
                    Notification::create([
                        'role' => 'technician',
                        'user_id' => $oldTech->id,
                        'icon' => 'tool',
                        'title' => "Repair {$repair->id} unassigned",
                        'message' => "{$repair->device} was unassigned from your queue.",
                        'upcoming' => false,
                        'time_label' => 'Just now',
                        'target' => 'assigned',
                        'entity_id' => $repair->id,
                        'is_read' => false,
                    ]);
                }
            }

            $repair->technician_id = null;
            $repair->technician_name = 'Unassigned';
            $repair->save();
        }

        return response()->json([
            'success' => true,
            'repair' => $repair,
            'message' => $targetTechName ? "Job {$repair->id} moved to {$targetTechName}. In-web and email notifications sent." : "Job {$repair->id} unassigned.",
        ]);
    }

    public function receivePackage(Request $request, string $id)
    {
        $repair = Repair::findOrFail($id);

        if ($repair->method !== 'Mail-in') {
            return response()->json([
                'success' => false,
                'message' => 'Only mail-in deliveries require package receipt confirmation.',
            ], 400);
        }

        $oldStatus = $repair->status;
        $repair->status = 'Package received';
        $repair->slip_verified = true;
        $repair->active_step = 0;
        $repair->latest_update = 'Package received and checked into Fixsmith intake. Ready for technician assignment.';
        $repair->save();

        // Create activity center notification for customer
        Notification::create([
            'role' => 'customer',
            'user_id' => $repair->customer_id,
            'icon' => 'box',
            'title' => "Package received: {$repair->id}",
            'message' => "Your device has arrived at Fixsmith workshop and passed intake inspection.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'repairs',
            'entity_id' => $repair->id,
            'is_read' => false,
        ]);

        // Dispatch Email Notification via SMTP
        MailNotificationService::notifyStatusUpdated(
            $repair,
            $oldStatus,
            'Package received',
            'Package received and checked into Fixsmith intake. Ready for technician assignment.'
        );

        return response()->json([
            'success' => true,
            'repair' => $repair,
            'message' => "Package for {$repair->id} confirmed and marked as 'Package received'. Technician assignment is now enabled.",
        ]);
    }
}
