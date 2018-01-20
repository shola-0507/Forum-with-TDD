<?php

namespace App\Policies;

use App\User;
use App\Thread;
use Illuminate\Auth\Access\HandlesAuthorization;

class ThreadPolicy
{
    use HandlesAuthorization;

    /*This has been implemented in the boot method of the authserviceprovider class.
    public function before($user) {
        if ($user->name =+= 'test') {
           return true;
        }
    }*/

    /**
     * Determine whether the user can view the threads.
     *
     * @param  \App\User  $user
     * @param  \App\Threads  $threads
     * @return mixed
     */
    public function view(User $user, Thread $thread)
    {
        //
    }

    /**
     * Determine whether the user can create threads.
     *
     * @param  \App\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        //
    }

    /**
     * Determine whether the user can update the threads.
     *
     * @param  \App\User  $user
     * @param  \App\Threads  $threads
     * @return mixed
     */
    public function update(User $user, Thread $thread)
    {
        return $thread->user_id == $user->id;
    }

    /**
     * Determine whether the user can delete the threads.
     *
     * @param  \App\User  $user
     * @param  \App\Threads  $threads
     * @return mixed
     */
    public function delete(User $user, Thread $thread)
    {
        //
    }
}
