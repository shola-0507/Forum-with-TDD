<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmation_token'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email', 'confirmation_token'
    ];

    protected $casts = ['confirmed' => 'boolean'];

    public function getRouteKeyName() {

        return 'name';
    }

    public function confirm() {
        $this->confirmed = true;
        $this->confirmation_token = null;
        $this->save();
    }

    public function threads() {

        return $this->hasMany(Thread::class)->latest();
    }

    public function lastReply() {

        return $this->hasOne(Reply::class)->latest();
    }

    public function Activity() {

        return $this->hasMany(Activity::class);
    }

    public function VisitedThreadCacheKey($thread) {

        return $key = sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    public function getAvatarPathAttribute($avatar) {

        return $avatar ? '/storage/' . $avatar : '/images/avatars/default.jpg';
    }

    public function read($thread) {

        cache()->forever(
            $this->VisitedThreadCacheKey($thread), 
            \Carbon\Carbon::now()
        );
    }
}
