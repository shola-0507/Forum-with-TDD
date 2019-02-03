<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class UserTest extends TestCase
{
	use DatabaseMigrations; 

    /** @test */
    public function a_user_can_fetch_their_last_reply()
    {
    	$user = create('App\User');

    	$reply = create('App\Reply', ['user_id' => $user->id]);

        $this->assertEquals($user->lastReply->id, $reply->id);
    }

    /** @test */
    public function a_user_can_determine_their_avatar_path() {

    	$user = create('App\User');

    	$this->assertEquals($user->avatar_path, '/images/avatars/default.jpg');

    	$user->avatar_path = 'avatars/avatar.jpg';

    	$this->assertEquals($user->avatar_path, '/storage/avatars/avatar.jpg');
    }
}
