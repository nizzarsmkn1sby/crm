<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        // AJAX polling untuk count notifikasi
        if ($request->wantsJson() || $request->get('count')) {
            return response()->json([
                'unread' => $user->unreadNotifications()->count(),
            ]);
        }

        $notifications = $user->notifications()->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    public function markAsRead($id)
    {
        Auth::user()->notifications()->where('id', $id)->first()?->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return back();
    }

    public function markAllRead()
    {
        Auth::user()->unreadNotifications->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['success' => true, 'unread' => 0]);
        }

        return back()->with('success', 'Semua notifikasi sudah dibaca!');
    }
}
