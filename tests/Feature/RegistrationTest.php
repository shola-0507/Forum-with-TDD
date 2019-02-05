<?php

namespace Tests\Feature;

use App\User;
use App\Mail\PleaseConfirmYourEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;


class RegistrationTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function a_confirmation_email_is_sent_upon_registration()
    {           
        Mail::fake();

        event(new Registered(create('App\User')));

        Mail::assertQueued(PleaseConfirmYourEmail::class);
    }

    /** @test */
    public function users_can_fully_confirm_their_email_addresses()
    {
        Mail::fake(); 

    	$this->post(route('register'), [
    		'name' => 'John',
    		'email' => 'john@example.com',
    		'password' => 'foobar',
    		'password_confirmation' => 'foobar'
    	]);

    	$user = User::whereName('John')->first();

    	$this->assertFalse($user->confirmed);

    	$this->assertNotNull($user->confirmation_token);

    	$response = $this->get(route('register.confirm', 
    		['token' => $user->confirmation_token]))
    		->assertRedirect(route('threads'));

        tap($user->fresh(), function($user) {
            $this->assertTrue($user->confirmed);
            $this->assertNull($user->confirmation_token);
        });
    }

    /** @test */
    public function confirming_an_invalid_token() {

        $this->get(route('register.confirm', ['token' => 'invalid-token']))
            ->assertRedirect(route('threads'))
            ->assertSessionHas('flash', 'Invalid Token');
    }

}
