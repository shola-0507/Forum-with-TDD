<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Http\Request;

class UserConfirmationController extends Controller
{
    public function index() {
		$user = User::where('confirmation_token', request('token'))->first();
		
		if (! $user) {
			return redirect('/threads')->with('flash', 'Invalid Token');
		}

		$user->confirm();
	
    	return redirect('/threads')->with('flash', 'Your account has been confirmed, You can now post to the forum');
    }
}
