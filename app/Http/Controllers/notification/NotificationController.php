<?php

namespace App\Http\Controllers\notification;

use App\Http\Controllers\Controller;
use App\Models\SystemNotifications;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class NotificationController extends Controller
{
    public function __construct(
        protected NotificationService $notifications
    ) {}

    public function checkNew(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['notifications' => [], 'latest_id' => 0, 'count' => 0, 'unread_total' => 0]);
        }

        $lastSeenId = (int) $request->query('last_id', 0);

        $newNotifications = $this->notifications->newSince($user, $lastSeenId);
        $latestId = SystemNotifications::forUser($user->id)->max('id') ?? 0;
        $unreadItems = $this->notifications->unreadForUser($user, 50);

        return response()->json([
            'notifications' => $newNotifications->map(fn ($note) => $this->formatNotification($note)),
            'unread_notifications' => $unreadItems
                ->map(fn ($note) => $this->formatNotification($note))
                ->values(),
            'latest_id'     => (int) $latestId,
            'count'         => $newNotifications->count(),
            'unread_total'  => $this->notifications->unreadCount($user),
        ]);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['notifications' => [], 'unread_total' => 0]);
        }

        $limit = min((int) $request->query('limit', 20), 50);
        $items = $this->notifications->unreadForUser($user, $limit);

        return response()->json([
            'notifications' => $items->map(fn ($note) => $this->formatNotification($note)),
            'unread_total'  => $this->notifications->unreadCount($user),
        ]);
    }

    public function markRead(int $id)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $updated = $this->notifications->markRead($id, $user);

        return response()->json([
            'success'      => $updated,
            'unread_total' => $this->notifications->unreadCount($user),
        ]);
    }

    public function markAllRead()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['success' => false], 401);
        }

        $count = $this->notifications->markAllRead($user);

        return response()->json([
            'success'      => true,
            'marked'       => $count,
            'unread_total' => 0,
        ]);
    }

    protected function formatNotification(SystemNotifications $note): array
    {
        $actionUrl = $note->action_url ?: $note->link;

        if ($note->action_route && Route::has($note->action_route)) {
            $actionUrl = route($note->action_route);
        }

        return [
            'id'          => $note->id,
            'title'       => $note->title,
            'message'     => $note->message,
            'type'        => $note->type,
            'reference_id'=> $note->reference_id,
            'action_url'  => $actionUrl,
            'action_route'=> $note->action_route,
            'read_at'     => $note->read_at?->toIso8601String(),
            'is_unread'   => $note->isUnread(),
            'created_at'  => $note->created_at?->toIso8601String(),
            'created_human'=> $note->created_at?->diffForHumans(),
        ];
    }
}
