<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 25);
        $limit = max(1, min($limit, 100));
        $onlyUnread = filter_var($request->query('unread'), FILTER_VALIDATE_BOOL);

        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('notifications:view')) {
            abort(403, __('No tienes permisos para consultar notificaciones.'));
        }

        $query = $onlyUnread
            ? $user->unreadNotifications()
            : $user->notifications();

        $notifications = $query
            ->latest()
            ->limit($limit)
            ->get()
            ->map(function (DatabaseNotification $notification): array {
                return $this->formatNotification($notification);
            });

        return response()->json([
            'data' => $notifications,
            'meta' => [
                'unread_count' => $user->unreadNotifications()->count(),
            ],
        ]);
    }

    public function markAsRead(Request $request, string $notificationId): JsonResponse
    {
        $user = $request->user();

        if (method_exists($user, 'tokenCan') && ! $user->tokenCan('notifications:view')) {
            abort(403, __('No tienes permisos para consultar notificaciones.'));
        }

        $notification = $user
            ->notifications()
            ->whereKey($notificationId)
            ->firstOrFail();

        if ($notification->read_at === null) {
            $notification->markAsRead();
        }

        $notification->refresh();

        return response()->json([
            'data' => $this->formatNotification($notification),
        ]);
    }

    private function formatNotification(DatabaseNotification $notification): array
    {
        return [
            'id' => $notification->id,
            'type' => class_basename($notification->type),
            'data' => $notification->data,
            'read_at' => $notification->read_at?->toIso8601String(),
            'created_at' => $notification->created_at?->toIso8601String(),
            'updated_at' => $notification->updated_at?->toIso8601String(),
        ];
    }
}
