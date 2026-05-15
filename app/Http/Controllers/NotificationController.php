<?php
namespace App\Http\Controllers;
use App\Traits\ApiResponse;
use App\Models\Notification;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    use ApiResponse;

    public function index(Request $request){
        $notifications = Notification::where('user_id', $request->user()->user_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return $this->success($notifications);
    }

    public function unreadCount(Request $request){
        $count = Notification::where('user_id', $request->user()->user_id)
            ->where('is_read', false)
            ->count();
        return $this->success(['unread_count' => $count], 'Unread count fetched');
    }

    public function markAsRead($id){
        $notification = Notification::find($id);
        if(!$notification){
            return $this->error('Notification not found', 404);
        }
        if($notification->is_read){
            return $this->success($notification, 'Notification already read');
        }
        $notification->update(['is_read' => true]);
        return $this->success($notification, 'Notification marked as read');
    }

    public function markAllAsRead(Request $request){
        $unreadCount = Notification::where('user_id', $request->user()->user_id)
            ->where('is_read', false)
            ->count();
        if($unreadCount === 0){
            return $this->success(null, 'No unread notifications');
        }
        Notification::where('user_id', $request->user()->user_id)
            ->update(['is_read' => true]);
        return $this->success(null, 'All notifications marked as read');
    }
}
