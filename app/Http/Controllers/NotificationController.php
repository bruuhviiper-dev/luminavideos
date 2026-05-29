<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()
            ->orderByDesc('created_at')
            ->paginate(20);

        // Mark all as read
        Auth::user()->notifications()->where('is_read', false)->update(['is_read' => true]);

        $categories = \App\Services\CacheService::remember(
            \App\Services\CacheService::categoriesKey(), 60, fn() => \App\Models\Category::all()
        );

        return view('notifications', compact('notifications', 'categories'));
    }

    public function markRead($id)
    {
        Notification::where('id', $id)->where('user_id', Auth::id())->update(['is_read' => true]);
        return response()->json(['ok' => true]);
    }

    public function markAllRead()
    {
        Auth::user()->notifications()->update(['is_read' => true]);
        return back()->with('status', 'Notificações marcadas como lidas.');
    }
}
