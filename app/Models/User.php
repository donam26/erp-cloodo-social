<?php

namespace App\Models;

use App\Enums\FriendStatus;
use App\Traits\UuidTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;
    use UuidTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    /**
     * Kiểm tra trạng thái k�t bạn với một user
     * 
     * @param string $friendId ID của user c�n kiểm tra
     * @return object|null Trả về record trong bảng friends hoặc null
     */
    public function checkFriend($friendId)
    {
        if ($this->id === $friendId) {
            return null; // Không thể kết bạn với chính mình
        }

        return DB::table('friends')
            ->where(function ($query) use ($friendId) {
                $query->where(function ($q) use ($friendId) {
                    $q->where('user_id', $this->id)
                        ->where('friend_id', $friendId);
                })->orWhere(function ($q) use ($friendId) {
                    $q->where('user_id', $friendId)
                        ->where('friend_id', $this->id);
                });
            })
            ->first();
    }

    public function friends()
    {
        return DB::table('friends')
            ->join('users', function ($join) {
                $join->on('friends.friend_id', '=', 'users.id')
                    ->where('friends.user_id', '=', $this->id)
                    ->orOn('friends.user_id', '=', 'users.id')
                    ->where('friends.friend_id', '=', $this->id)
                    ->where('friends.type', '=', 'accepted');
            })
            ->select('users.*');
    }

    public function suggests()
    {
        return DB::table('users')
            ->whereNotIn('users.id', function ($query) {
                $query->select('friend_id')
                    ->from('friends')
                    ->where('user_id', $this->id)
                    ->whereIn('type', [
                        FriendStatus::Pending->value,
                        FriendStatus::Accepted->value,
                        FriendStatus::Blocked->value
                    ]);
            })
            ->whereNotIn('users.id', function ($query) {
                $query->select('user_id')
                    ->from('friends')
                    ->where('friend_id', $this->id)
                    ->whereIn('type', [
                        FriendStatus::Pending->value,
                        FriendStatus::Accepted->value,
                        FriendStatus::Blocked->value
                    ]);
            })
            ->where('users.id', '!=', $this->id)
            ->select('users.*');
    }

    public function waitAccepts()
    {
        return DB::table('users')
            ->join('friends', 'users.id', '=', 'friends.friend_id')
            ->where('friends.user_id', $this->id)
            ->where('friends.type', FriendStatus::Pending->value)
            ->select('users.*');
    }



    public function posts()
    {
        return $this->hasMany(Post::class);
    }
    public function newsFeed()
    {
        return Post::query()
            ->where(function ($query) {
                // Posts của bản thân
                $query->where('posts.user_id', $this->id)
                    // Posts của bạn bè
                    ->orWhereIn('posts.user_id', function ($subQuery) {
                        $subQuery->select('friends.friend_id')
                            ->from('friends')
                            ->where('friends.user_id', $this->id)
                            ->where('friends.status', FriendStatus::Accepted->value)
                            ->union(
                                DB::table('friends')
                                    ->select('friends.user_id')
                                    ->where('friends.friend_id', $this->id)
                                    ->where('friends.status', FriendStatus::Accepted->value)
                            );
                    });
            })
            ->with(['author', 'comments', 'reactions', 'images'])
            ->orderBy('created_at', 'desc');
    }

    public function stories()
    {
        return Story::query()
            ->where(function ($query) {
                // Stories của bản thân
                $query->where('stories.user_id', $this->id)
                    // Stories của bạn bè
                    ->orWhereIn('stories.user_id', function ($subQuery) {
                        $subQuery->select('friends.friend_id')
                            ->from('friends')
                            ->where('friends.user_id', $this->id)
                            ->where('friends.type', FriendStatus::Accepted->value)
                            ->union(
                                DB::table('friends')
                                    ->select('friends.user_id')
                                    ->where('friends.friend_id', $this->id)
                                    ->where('friends.type', FriendStatus::Accepted->value)
                            );
                    });
            })
            ->with(['author']) 
            ->orderBy('created_at', 'desc');
    }
}
