<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\NotificationResource;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct(protected Notification $model) {}

    public function index(Request $request, $id = null)
    {
        if ($id) {
            $notification = $this->model->with('users')->find($id);

            if (!$notification) {
                return sendResponse(null, 404, 'Notification not found');
            }

            return sendResponse(new NotificationResource($notification), 200);
        }

        $notifications = $this->model
            ->when($request->query('with') == 'users', function ($query) {
                $query->with('users');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20, ['*'], 'page', $request->page);

        return sendResponse(NotificationResource::collection($notifications), 200);
    }

    public function myNotifications(Request $request)
    {

    info(Auth::user());
        $notifications = Auth::user()
            ->notifications()
            ->orderByPivot('created_at', 'desc')
            ->paginate(20, ['*'], 'page', $request->page);

        return sendResponse(NotificationResource::collection($notifications), 200);
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'message' => ['required', 'string'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['required', 'exists:users,id'],
            'send_to_all' => ['nullable', 'boolean'],
        ]);

        $notification = $this->model->create([
            'title' => $request->title,
            'message' => $request->message,
        ]);

        $userIds = $request->boolean('send_to_all')
            ? User::pluck('id')->toArray()
            : $request->input('user_ids', []);

        if (count($userIds) > 0) {
            $notification->users()->sync($userIds);
        }

        return sendResponse(new NotificationResource($notification->load('users')), 201, 'Notification created successfully!');
    }

    public function update(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:notifications,id'],
            'title' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string'],
            'user_ids' => ['nullable', 'array'],
            'user_ids.*' => ['required', 'exists:users,id'],
            'send_to_all' => ['nullable', 'boolean'],
        ]);

        $notification = $this->model->find($request->id);

        $request->title ? $notification->title = $request->title : null;
        $request->message ? $notification->message = $request->message : null;
        $notification->save();

        if ($request->boolean('send_to_all')) {
            $notification->users()->sync(User::pluck('id')->toArray());
        } else if ($request->has('user_ids')) {
            $notification->users()->sync($request->user_ids);
        }

        return sendResponse(new NotificationResource($notification->load('users')), 200, 'Notification updated successfully!');
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:notifications,id'],
        ]);

        $notification = $this->model->find($request->id);
        $notification->delete();

        return sendResponse(null, 200, 'Notification deleted successfully!');
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'id' => ['required', 'exists:notifications,id'],
        ]);

        $user = Auth::user();
        $notification = $user->notifications()->where('notifications.id', $request->id)->first();

        if (!$notification) {
            return sendResponse(null, 404, 'Notification not found');
        }

        $user->notifications()->updateExistingPivot($request->id, [
            'is_read' => true,
            'read_at' => now(),
        ]);

        $notification = $user->notifications()->where('notifications.id', $request->id)->first();

        return sendResponse(new NotificationResource($notification), 200, 'Notification marked as read!');
    }

    public function markAllAsRead()
    {
        DB::table('notifications_user')
            ->where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
                'updated_at' => now(),
            ]);

        return sendResponse(null, 200, 'All notifications marked as read!');
    }

    public function unreadCount()
    {
        $count = Auth::user()
            ->notifications()
            ->wherePivot('is_read', false)
            ->count();

        return sendResponse(['count' => $count], 200);
    }
}
