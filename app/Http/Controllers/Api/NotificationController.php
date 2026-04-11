<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreNotificationRequest;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use AuthorizesRequests;

    protected NotificationService $service;

    public function __construct(NotificationService $service)
    {
        $this->service = $service;
    }

    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 15);
        $notifications = $this->service->getQueryForUser(auth()->id())
            ->paginate($perPage);

        return NotificationResource::collection($notifications);
    }

    public function store(StoreNotificationRequest $request)
    {
        $this->authorize('create', Notification::class);
        $notification = $this->service->create($request->validated());
        return new NotificationResource($notification);
    }

    public function show(Notification $notification)
    {
        $this->authorize('view', $notification);
        $this->service->markAsRead($notification, auth()->user());
        return new NotificationResource($notification);
    }

    public function destroy(Notification $notification)
    {
        $this->authorize('delete', $notification);
        $notification->delete();
        return response()->json(["message" => "Уведомление удалено."]);
    }
}
