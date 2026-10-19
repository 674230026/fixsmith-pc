<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $role = $request->input('role', 'customer');
        $notifications = Notification::where('role', $role)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($notifications);
    }

    public function markAsRead(Request $request, int $id)
    {
        $notification = Notification::findOrFail($id);
        $notification->is_read = true;
        $notification->save();

        return response()->json(['success' => true]);
    }

    public function markAllAsRead(Request $request)
    {
        $role = $request->input('role', 'customer');
        Notification::where('role', $role)->update(['is_read' => true]);

        return response()->json(['success' => true]);
    }
}
