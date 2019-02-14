<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LockThreadsTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function non_administrators_may_not_lock_threads() {
        $this->signIn();

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'locked' => true
        ])->assertStatus(302);

        $this->assertFalse(!! $thread->fresh()->locked);
    }

    /** @test */
    public function administrators_may_lock_threads() {
        $this->signIn(factory('App\User')->states('administrator')->create());

        $thread = create('App\Thread', ['user_id' => auth()->id()]);

        $this->patch($thread->path(), [
            'locked' => true
        ])->assertStatus(200);

        $this->assertTrue(!! $thread->fresh()->locked);
    }

    /** @test */
    public function once_locked_a_thread_may_not_receive_new_replies() {

        $this->signIn();

        $thread = create('App\Thread');

        $thread->locked();

        $this->post($thread->path() . '/replies', [
            'body' => 'foobar',
            'user_id' => auth()->id()
        ])->assertStatus(422);
    }
}
