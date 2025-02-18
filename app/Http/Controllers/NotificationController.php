<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(15);
        return $this->successResponse(
            NotificationResource::collection($notifications),
            'Lấy danh sách thông báo thành công'
        );
    }

    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return $this->successResponse(
            new NotificationResource($notification),
            'Đánh dấu đã đọc thành công'
        );
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        
        return $this->successResponse(
            null,
            'Đánh dấu tất cả là đã đọc thành công'
        );
    }

    public function destroy($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->delete();
        
        return $this->successResponse(
            null,
            'Xóa thông báo thành công'
        );
    }

    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        return $this->successResponse(
            null,
            'Đánh dấu đã đọc thành công'
        );
    }
}
