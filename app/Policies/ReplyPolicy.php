<?php

namespace App\Policies;

use App\User;
use App\Reply;
use Illuminate\Auth\Access\HandlesAuthorization;

class ReplyPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function update(User $user, Reply $reply){
        
        return $reply->user_id == $user->id;
    }

    public function create(User $user) {

        $lastreply = $user->fresh()->lastreply;

        if ( ! $lastreply )  return true;

    	return ! $lastreply->wasJustPublished();
    }
}
