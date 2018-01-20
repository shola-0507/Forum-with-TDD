<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class FavouritesTest extends TestCase
{
	use DatabaseMigrations;

	/** @test */
	public function guests_cannot_favourite_anything() {
		$this->withExceptionHandling()
			->post('replies/1/favourite')
			->assertRedirect('/login');
	}

    /** @test */
    public function an_authenticated_user_can_favourite_any_reply() {

    	$this->signIn();

        $reply = create('App\Reply');

        $this->post('replies/' . $reply->id . '/favourite');

        $this->assertCount(1, $reply->favourites);
    }

    /** @test */
    public function an_authenticated_user_can_unfavourite_any_reply() {

        $this->signIn();

        $reply = create('App\Reply');

        $reply->favourite();

        $this->delete('replies/' . $reply->id . '/favourite');

        $this->assertCount(0, $reply->favourites);
    }

    /** @test */
    public function an_authenticated_user_may_favourite_a_reply_only_once() {

    	$this->signIn();

        $reply = create('App\Reply');

        try {
        	$this->post('replies/' . $reply->id . '/favourite');
        	$this->post('replies/' . $reply->id . '/favourite');
        } catch (\Exception $e) {
        	$this->fail('Did not expect to insert the same record twice.');
        }

        $this->assertCount(1, $reply->favourites);
    }

}
