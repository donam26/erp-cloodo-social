<?php

namespace App\Enums;

enum FriendStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Blocked = 'blocked';
    case Cancel = 'cancel';

    case SendRequest = 'Đã gửi yêu cầu kết bạn';
    case BlockRequest = 'Đã chặn người dùng';
    case AcceptRequest = 'Đã chấp nhận kết bạn';
    case CancelRequest = 'Đã xóa bạn bè';
    case UnblockRequest = 'Đã bỏ chặn bạn bè';
}
