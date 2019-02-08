<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\RefreshDatabase;

class BestReplyTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function a_thread_creator_may_mark_any_reply_as_the_best_reply()
    {
    	$user = create('App\User');

    	$this->signIn($user);

    	$thread = create('App\Thread', ['user_id' => $user->id]);

    	$replies = create('App\Reply', ['thread_id' => $thread->id], 2);

    	$this->postJson(route('best-replies.store', ['reply' => $replies[1]->id]));

        $this->assertTrue($replies[1]->fresh()->isBest());
    }

    /** @test */
    public function only_the_thread_creator_can_make_the_reply_as_best() {

    	$this->withExceptionHandling();

    	$this->signIn();

    	$thread = create('App\Thread', ['user_id' => auth()->id()]);

    	$replies = create('App\Reply', ['thread_id' => $thread->id], 2);

    	$this->signIn(create('App\User'));

    	$this->postJson(route('best-replies.store', ['reply' => $replies[1]->id]))
    		->assertStatus(403);

        $this->assertFalse($replies[1]->fresh()->isBest());
    }
}
