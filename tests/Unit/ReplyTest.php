<?php

namespace Tests\Unit;

use App\Reply;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class ReplyTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    function it_has_an_owner(){
    	$reply = factory('App\Reply')->create();

    	$this->assertInstanceOf('App\User', $reply->owner);
    }

    /** @test */
    public function it_knows_if_the_reply_was_just_published() {

    	$reply = factory('App\Reply')->create();

    	$this->assertTrue($reply->wasJustPublished());
    }

    /** @test */
    function it_can_detect_mentioned_users(){

        $reply = create('App\Reply', [
            'body' => '@JaneDoe wants to talk to @JohnDoe'
        ]);

        $mentionedUsers = $reply->mentionedUsers();

        $this->assertEquals(['JaneDoe', 'JohnDoe'], $reply->mentionedUsers());
    }

    /** @test */
    function it_replaces_usernames_within_replies_within_anchortags() {

        $reply = new Reply([
            'body' => 'Hello @Jane-Doe.'
        ]);

        $this->assertEquals(
            'Hello <a href="/profiles/Jane-Doe">@Jane-Doe</a>.', 
            $reply->body
        );
    }

    /** @test */
    public function it_knows_if_it_is_the_best_reply() {

        $reply = create('App\Reply');

        $this->assertFalse($reply->isBest());

        $reply->thread->update(['best_reply_id' => $reply->id]);

        $this->assertTrue($reply->fresh()->isBest());        
    }
}
