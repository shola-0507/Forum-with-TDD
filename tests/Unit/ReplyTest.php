<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

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
}
