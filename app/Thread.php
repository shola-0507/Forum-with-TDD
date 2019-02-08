<?php

namespace App;

use App\Events\ThreadRecievedNewReply;
use App\Events\ThreadHasNewReply;
use Illuminate\Database\Eloquent\Model;
use App\Notifications\ThreadWasUpdated;

class Thread extends Model
{

    use RecordsActivity, RecordsVisits;

	protected $guarded = [];

    protected $with = ['channel'];

    protected $appends = ['isSubscribedTo'];

    protected static function boot() {

        parent::boot();

        static::addGlobalScope('creator', function ($builder) {

            $builder->with('creator');
        });

        static::deleting(function ($thread) {
            $thread->replies->each->delete();
        });

        static::created(function ($thread) {
            $thread->update(['slug' => $thread->title]);
        });
    }

    public function path() {

    	return "/threads/{$this->channel->slug}/{$this->slug}";
    }

    public function replies() {

    	return $this->hasMany(Reply::class);
    }

    public function creator() {

    	return $this->belongsTo(User::class, 'user_id');
    }

    public function channel() {
        
        return $this->belongsTo(Channel::class);
    }

    public function addReply($reply) {

    	$reply = $this->replies()->create($reply);

        event(new ThreadHasNewReply($this, $reply));

        event(new ThreadRecievedNewReply($reply));

        return $reply;
    }

    public function scopeFilter($query, $filters) {

        return $filters->apply($query);
    }

    public function subscribe($userId = null) {

        $this->subscriptions()->create([

            'user_id' => $userId ?: auth()->id()
        ]);

        return $this;
    }

    public function unsubscribe($userId = null) {

        $this->subscriptions()
            ->where('user_id', $userId ?: auth()->id())
            ->delete();                      
    }

    public function subscriptions() {

        return $this->hasMany(ThreadSubscription::class);
    }

    public function getIsSubscribedToAttribute() {

        return $this->subscriptions()
            ->where('user_id', auth()->id())
            ->exists();
    }

    public function hasUpdatesFor($user) {

        $key = $user->VisitedThreadCacheKey($this);

        return $this->updated_at > cache($key);
    }

    public function getRouteKeyName() {
        return 'slug'; 
    }

    public function setSlugAttribute($value) {

        $slug = str_slug($value);

        if (static::whereSlug($slug)->exists()) {
             $slug = "{$slug}-" . $this->id;
         } 
 
        $this->attributes['slug'] =  $slug;
    }

    public function markBestReply() {
        $this->update(['best_reply_id' => $reply->id]);
    }
}
