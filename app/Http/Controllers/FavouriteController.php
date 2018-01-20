<?php

namespace App\Http\Controllers;

use App\Favourite;
use App\Reply;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
	public function __construct(){

		$this->middleware('auth');
	}

    public function store(Reply $reply) {

    	/*This works because the reply and the favourite classes have a polymorphic one to many relationship. ELoquent automatically sets the reply id and the class for us.*/

    	$reply->favourite();

        return back();

    	/*Favourite::create([
    		'user_id' => auth()->id(),
    		'favourited_id' => $reply->id,
    		'favourited_type' => get_class($reply)
    	]);*/
    }

    public function destroy(Reply $reply) {

        $reply->unfavourite();
    }
}
