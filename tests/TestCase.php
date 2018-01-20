<?php

namespace Tests;

use App\Exceptions\Handler;
use Illuminate\Contracts\Debug\ExceptionHandler;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;

    protected function setUp()
    {
        parent::setUp();
        $this->disableExceptionHandling();
    }


	protected function signIn($user = null) {

		$user = $user ?: create('App\User');

		$this->actingAs($user);

		return $this;
	}

	protected function disableExceptionHandling()
    {
        $this->oldExceptionHandler = $this->app->make(ExceptionHandler::class);
        $this->app->instance(ExceptionHandler::class, new PassThroughHandler);
    }
    
    /**
     * (Re-) Enable exception handling.
     *
     * @author Adam Wathan {@link https://adamwathan.me/2016/01/21/disabling-exception-handling-in-acceptance-tests/}
     * @return Tests\TestCase
     */
    protected function withExceptionHandling()
    {
        $this->app->instance(ExceptionHandler::class, $this->oldExceptionHandler);
        return $this;
    }
}

class PassThroughHandler extends Handler {
    public function __construct() {}
    public function report(\Exception $e) {}
    public function render($request, \Exception $e) { throw $e; }
}