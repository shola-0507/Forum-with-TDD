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
}
