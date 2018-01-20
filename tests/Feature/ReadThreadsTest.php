<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class ReadThreadsTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {

        parent::setUp();

        $this->thread = factory('App\Thread')->create();
    }

    /** @test */
    public function a_user_can_read_all_threads() {

        $response = $this->get('/threads')->assertSee($this->thread->title);

    }

    /** @test */
    public function a_user_can_read_a_single_thread(){
 
    	$this->get($this->thread->path())->assertSee($this->thread->title);

    }

    /** @test */
    public function a_user_can_filter_threads_according_to_a_channel(){

        $thread = create('App\Thread');
        $threadInChannel = create('App\Thread', ['channel_id' => $thread->channel->id]);
        $threadNotInChannel = create('App\Thread');

        $this->get('/threads/' . $thread->channel->slug)
            ->assertSee($threadInChannel->title)
            ->assertDontSee($threadNotInChannel->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_username() {

        $this->signIn(create('App\User', ['name' => 'JohnDoe']));

        $threadByJohn = create('App\Thread', ['user_id' => auth()->id()]);
        $threadNotByJohn = create('App\Thread');

        $this->get('threads?by=JohnDoe')
            ->assertSee($threadByJohn->title)
            ->assertDontSee($threadNotByJohn->title);
    }

    /** @test */
    function a_user_can_filter_threads_by_popularity() {

        $threadWithTwoReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithTwoReplies->id], 2);

        $threadWithThreeReplies = create('App\Thread');
        create('App\Reply', ['thread_id' => $threadWithThreeReplies->id], 3);

        $threadWithNoReplies = $this->thread;

        $response = $this->getJson('threads?popular=1')->json();

        //dd(array_column($response['data'], 'replies_count'));

        $this->assertEquals([3, 2, 0], array_column($response, 'replies_count'));
    }

    /** @test */
    function a_user_can_filter_threads_by_those_that_are_unanswered() {

        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id]);

        $response = $this->getJson('threads?unanswered=1')->json();
        $this->assertCount(1, $response);
    }

    /** @test */
    function a_user_can_request_all_replies_for_a_given_thread() {

        $thread = create('App\Thread');
        create('App\Reply', ['thread_id' => $thread->id], 3);

        $response = $this->getJson($thread->path() . '/replies')->json();

        $this->assertCount(3, $response['data']);
        $this->assertEquals(3, $response['total']);

    }

}
