<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Forms\CreatePostForm;
use App\Reply;
use App\Thread;
use Exception;
use Gate;

class ReplyController extends Controller
{

	public function __construct(){

		$this->middleware('auth', ['except' => 'index']);
	}

    public function index($channelId, Thread $thread) {

        return $thread->replies()->paginate(20);
    }

    public function store($channelId, Thread $thread, CreatePostForm $form){

        $reply = $thread->addReply([
            'body' => request('body'),
            'user_id' => auth()->id()
        ])->load('owner');

    }

    public function update(Reply $reply) {

        $this->authorize('update', $reply);
        $this->validate(request(), ['body' => 'required|SpamFree']);

        try {
        
            $reply->update(request(['body']));
        } catch(\Exception $e) {

            return response('Sorry we are not able to post your reply at this time', 422);
        }

        
    }

    public function destroy(Reply $reply) {

        $this->authorize('update', $reply);
        
        $reply->delete();

        if (request()->expectsJson()) {
            return response(['status' => 'Reply deleted.']);
        }

        return back();
    }
}
