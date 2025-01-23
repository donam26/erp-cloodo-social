<?php

namespace App\Enums;

enum GroupMemberStatus: string
{
    case Pending = 'pending';
    case Accepted = 'accepted';
    case Rejected = 'rejected';

    case SendRequest = 'Đã gửi yêu cầu tham gia nhóm';
    case AcceptRequest = 'Đã chấp nhận yêu cầu tham gia nhóm';
    case RejectRequest = 'Đã từ chối yêu cầu tham gia nhóm';
}
