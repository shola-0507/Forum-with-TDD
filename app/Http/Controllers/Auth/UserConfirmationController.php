<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserConfirmationController extends Controller
{
    public function index() {
    	try{
    		User::where('confirmation_token', request('token'))
    		->firstOrFail()
    		->confirm();
    	} catch(\Exception $e) {
    		return redirect('/threads')
    		->with('flash', 'Invalid Token');
    	}

    	return redirect('/threads')->with('flash', 'Your account has been confirmed, You can now post to the forum');
    }
}
