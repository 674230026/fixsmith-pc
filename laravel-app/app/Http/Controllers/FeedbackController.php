<?php

namespace App\Http\Controllers;

use App\Models\Feedback;
use App\Models\Notification;
use App\Models\Repair;
use App\Models\User;
use App\Services\MailNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role');
        $query = Feedback::query()->orderBy('created_at', 'desc');

        if ($role === 'technician') {
            $techName = $request->input('tech', 'Alex Kim');
            $repairIds = Repair::where('technician_name', $techName)->pluck('id')->toArray();
            $query->whereIn('repair_id', $repairIds);
        }

        return response()->json($query->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'mode' => 'required|in:rating,complaint',
            'repair_id' => 'nullable|string',
            'rating' => 'nullable|integer|min:0|max:5',
            'subject' => 'nullable|string',
            'message' => 'required|string',
        ]);

        $user = Auth::user() ?: User::where('role', 'customer')->first();

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $attachmentPath = $request->file('attachment')->store('feedback/attachments', 'public');
        }

        $type = $request->mode === 'rating' ? 'Rating' : 'Complaint';
        $id = Feedback::generateId($type);
        $subject = $request->subject ?: ($type === 'Rating' ? "{$request->rating}-Star Rating on {$request->repair_id}" : "Complaint on {$request->repair_id}");

        $feedback = Feedback::create([
            'id' => $id,
            'type' => $type,
            'repair_id' => $request->repair_id ?: 'general',
            'user_id' => $user->id,
            'customer_name' => $user->name,
            'customer_email' => $user->email,
            'subject' => $subject,
            'message' => $request->message,
            'rating' => $type === 'Rating' ? (int)$request->rating : 0,
            'attachment' => $attachmentPath,
            'status' => 'New',
            'age_formatted' => 'Just now',
        ]);

        // Notifications
        Notification::create([
            'role' => 'admin',
            'user_id' => null,
            'icon' => 'star',
            'title' => "New {$type} {$id}",
            'message' => "{$user->name} submitted {$type} regarding {$feedback->repair_id}.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'complaints',
            'entity_id' => $id,
            'is_read' => false,
        ]);

        // Send Email Notification via SMTP
        MailNotificationService::notifyFeedbackSubmitted($feedback);

        return response()->json([
            'success' => true,
            'feedback' => $feedback,
            'message' => "{$type} {$id} submitted successfully.",
        ]);
    }

    public function reply(Request $request, string $id)
    {
        $feedback = Feedback::findOrFail($id);

        $request->validate([
            'reply' => 'required|string',
            'status' => 'nullable|string',
        ]);

        $feedback->admin_reply = $request->reply;
        $feedback->status = $request->status ?: ($feedback->type === 'Rating' ? 'Reviewed' : 'Resolved');
        $feedback->save();

        // Customer notification
        Notification::create([
            'role' => 'customer',
            'user_id' => $feedback->user_id,
            'icon' => 'mail',
            'title' => "Workshop response to {$feedback->id}",
            'message' => "The workshop administrator has replied to your {$feedback->type}.",
            'upcoming' => false,
            'time_label' => 'Just now',
            'target' => 'complaint',
            'entity_id' => $feedback->id,
            'is_read' => false,
        ]);

        // Send Email Notification to Customer via SMTP
        MailNotificationService::notifyComplaintReply($feedback, $request->reply);

        return response()->json([
            'success' => true,
            'feedback' => $feedback,
            'message' => "Response sent to {$feedback->customer_name} via email.",
        ]);
    }
}
