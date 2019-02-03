<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvatarTest extends TestCase
{
	use DatabaseMigrations;

    /** @test */
    public function only_authenticated_users_can_upload_their_avatar()
    {
    	$this->withExceptionHandling();

    	$this->json('POST', 'api/users/{id}/avatar')
      		->assertStatus(401);
    }

    /** @test */
    public function an_avatar_must_be_a_valid_image()
    {
    	$this->withExceptionHandling()->signIn();
    	
    	$this->json('POST', 'api/users/'. auth()->id() . '/avatar', [
    		'avatar' => 'not-a-valid-image'
    	])->assertStatus(422);
    }

    /** @test */
    public function an_authenticated_user_may_upload_an_image_to_their_profile()
    {
    	$this->signIn();

    	Storage::fake('public');
    	
    	$this->json('POST', 'api/users/'. auth()->id() . '/avatar', [
    		'avatar' => $file = UploadedFile::fake()->image('avatar.jpg'),
    	]);

    	$this->assertEquals('/storage/avatars/' . $file->hashName(), auth()->user()->avatar_path);

    	Storage::disk('public')->assertExists('avatars/' . $file->hashName());
    }
}
