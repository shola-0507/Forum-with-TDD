<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class MentionUsersTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function mentioned_users_in_a_reply_are_notified()
    {
    	$john = create('App\User', ['name' => 'JohnDoe']);

    	$this->signIn($john);

    	$jane = create('App\User', ['name' => 'JaneDoe']);

    	$thread = create('App\Thread');

    	$reply = make('App\Reply', ['body' => 'Hey @JaneDoe']);

    	$this->json('post', $thread->path() . '/replies', $reply->toArray());

        $this->assertCount(1, $jane->notifications);
    }

    /** @test */
    public function it_can_fetch_all_mentioned_users_starting_with_given_characters() {
        create('App\User', ['name' => 'Johnson Ajibade']);
        create('App\User', ['name' => 'John Olushola']);
        create('App\User', ['name' => 'Sharon Abimbola']);

        $result = $this->json('GET', '/api/users', ['name' => 'john']);

        $this->assertCount(2, $result->json());
    }
}
