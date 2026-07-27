<?php

namespace App\Http\Controllers\notification;

use App\Http\Controllers\Controller;
use App\Models\SystemNotifications;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function checkNew(Request $request)
{
    $lastSeenId = $request->query('last_id', 0);

    $newNotifications = SystemNotifications::where('id', '>', $lastSeenId)
        ->orderBy('id', 'asc')
        ->get();

    $latestId = SystemNotifications::max('id') ?? 0;

    return response()->json([
        'notifications' => $newNotifications,
        'latest_id'      => $latestId,
        'count'          => $newNotifications->count(),
    ]);
}
}
