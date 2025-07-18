<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function markRead($id)
    {
        $user = Auth::user();
        $notification = $user->notifications()->where('id', $id)->first();
        if ($notification) {
        $notification->markAsRead();
        }
        return response()->noContent();
    }

    public function getNotifications()
    {
        $user = Auth::user();
        $notifications = $user->notifications()->latest()->take(10)->get();
        $unreadCount = $user->unreadNotifications()->count();
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount,
        ]);
    }

    public function getUnreadCount()
    {
        $user = Auth::user();
        $unreadCount = $user->unreadNotifications()->count();
        return response()->json(['unread_count' => $unreadCount]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();
        $user->unreadNotifications->markAsRead();
        return response()->noContent();
    }
}
