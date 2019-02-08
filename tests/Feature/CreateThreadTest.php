<?php

namespace Tests\Feature;

use App\Activity;
use App\Thread;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class CreateThreadTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function guests_may_not_create_threads(){

        $this->withExceptionHandling();

        $this->get('/threads/create')
              ->assertRedirect('/login');
              
        $this->post(route('threads'))
              ->assertRedirect('/login');
    } 

    /** @test */
    public function an_authenticated_user_can_create_threads()
    {
        //An authenticated User
        $this->signIn();

        //A thread created by the user
        $thread  = make('App\Thread');

        //Persist the thread in the database
        $response = $this->post(route('threads'), $thread->toArray());

        //Go to the thread URL and check if the thread title and body can be found on the page
        $this->get($response->headers->get('location'))
             ->assertSee($thread->title)
        	 ->assertSee($thread->body);

    }

    /** @test */
    public function a_new_user_must_confirm_their_email_before_creating_threads() {

        $user = factory('App\User')->states('unconfirmed')->create();

        $this->withExceptionHandling()->signIn($user);

        $thread = make('App\Thread');

        return $this->post(route('threads'), $thread->toArray())
                ->assertRedirect('/threads')
                ->assertSessionHas('flash', 'You must confirm your email address');
    }

    /** @test */
    public function a_thread_requires_a_title(){

        $this->publishThread(['title' => null])
            ->assertSessionHasErrors('title');
    }

    /** @test */
    public function a_thread_requires_a_body(){

        $this->publishThread(['body' => null])
            ->assertSessionHasErrors('body');
    }

    /** @test */
    public function a_thread_requires_a_valid_channel(){

        factory('App\Channel', 2)->create();

        $this->publishThread(['channel_id' => null])
            ->assertSessionHasErrors('channel_id');

        $this->publishThread(['channel_id' => 999])
            ->assertSessionHasErrors('channel_id');
    }

    /** @test */
    public function unauthorized_users_may_not_delete_threads() {

        $this->withExceptionHandling();

        $thread = create('App\Thread');
        $this->delete($thread->path())->assertRedirect('/login');

        $this->signIn();
        $this->delete($thread->path())->assertStatus(403);    
    }

    /** @test */
    public function authorized_users_can_delete_threads() {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $reply = create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->json('DELETE', $thread->path());

        $response->assertStatus(204);
        $this->assertDatabaseMissing('threads', ['id' => $thread->id]);
        $this->assertDatabaseMissing('replies', ['id' => $reply->id]);
        $this->assertEquals(0, Activity::count());

    }

    /** @test */
    public function a_thread_should_have_a_unique_slug() {

        $this->signIn();

        $thread = create('App\Thread', ['title' => 'foo title']);

        $this->assertEquals($thread->fresh()->slug, 'foo-title');

        $thread = $this->postJson('/threads', $thread->toArray())->json();

        $this->assertEquals("foo-title-{$thread['id']}", $thread['slug']);
    }

    /** @test */
    public function a_thread_with_a_number_in_the_title_should_still_generate_a_proper_slug() {
        
        $this->signIn();

        $thread = create('App\Thread', ['title' => 'foo title 24']);

        $thread = $this->postJson('/threads', $thread->toArray())->json();

        $this->assertEquals("foo-title-24-{$thread['id']}", $thread['slug']);
    }

    public function publishThread($assertions = []) {

        $this->withExceptionHandling()->signIn();

        $thread = make('App\Thread', $assertions);

        return $this->post(route('threads'), $thread->toArray());

    }
}
