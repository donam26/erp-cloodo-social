<?php

namespace App\Enums;

enum ReactionType: string
{
    case Like = 'like';
    case Love = 'love';
    case Haha = 'haha';
    case Wow = 'wow';
    case Sad = 'sad';
    case Angry = 'angry';

}
