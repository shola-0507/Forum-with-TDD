<?php

namespace Tests\Feature;

use App\Inspections\Spam;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ParticipateInForum extends TestCase
{
    use DatabaseMigrations;

    /** @test*/
    public function unauthenticated_users_may_not_add_replies(){

        $this->withExceptionHandling()
                ->post('threads/some-channel/1/replies', [])
                ->assertRedirect('/login');

    }

    /** @test*/
    public function an_authenticated_user_may_participate_in_forum_threads()
    {
        //Given we have an authenticated user
		$this->signIn();

		//And an existing Thread
		$thread = create('App\Thread');

		//When the user adds a reply to the thread
        $reply = make('App\Reply');

        $this->post($thread->path().'/replies', $reply->toArray());

        //Then their reply would be visible on the page
        $this->assertDatabaseHas('replies', ['body' => $reply->body]);
        $this->assertEquals(1, $thread->fresh()->replies_count);
    }

    /** @test*/
    public function a_reply_requires_a_body(){

        $this->withExceptionHandling()->signIn();

        $thread = create('App\Thread');

        $reply = make('App\Reply', ['body' => null]);

        $this->post($thread->path() .'/replies', $reply->toArray())
            ->assertSessionHasErrors('body');

    }

    /** @test*/
    public function unauthorized_users_cannot_delete_replies() {

        $this->withExceptionHandling();
        
        $reply = create('App\Reply');

        $this->delete("/replies/{$reply->id}")
            ->assertRedirect('login');    

        $this->signIn()->delete("/replies/{$reply->id}")
            ->assertStatus(403);

    }

    /** @test*/
    public function authorized_users_can_delete_replies() {

        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $this->delete("/replies/{$reply->id}")->assertStatus(302);

        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        
        $this->assertEquals(0, $reply->thread->fresh()->replies_count);
            
    }

    /** @test*/
    public function unauthorized_users_cannot_update_replies() {

        $this->withExceptionHandling();
        
        $reply = create('App\Reply');

        $this->patch("/replies/{$reply->id}")
            ->assertRedirect('login');    

        $this->signIn()->patch("/replies/{$reply->id}")
            ->assertStatus(403);

    }

    /** @test*/
    public function authorized_users_can_update_replies() {

        $this->signIn();

        $reply = create('App\Reply', ['user_id' => auth()->id()]);

        $update = 'Changed Ya!';

        $this->patch("/replies/{$reply->id}", ['body' => $update]);

        $this->assertDatabaseHas('replies', ['id' => $reply->id, 'body' => $update]);
    }

    /** @test*/
    function replies_that_contain_spam_may_not_be_created() {

        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => 'Yahoo Customer Support']);

        $this->json('post', $thread->path() . '/replies', $reply->toArray())
                ->assertStatus(422);
    }

    /** @test*/
    public function a_user_may_only_reply_once_per_minute() {

        $this->withExceptionHandling();

        $this->signIn();

        $thread = create('App\Thread');
        $reply = make('App\Reply', ['body' => 'An innocent reply']);

        $this->post($thread->path() . '/replies', $reply->toArray())
                ->assertStatus(200);

        $this->post($thread->path() . '/replies', $reply->toArray())
                ->assertStatus(429);


    }
}
