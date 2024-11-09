<?php

namespace App\Http\Controllers\Feed;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\Feed;
use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\returnCallback;

class FeedController extends Controller
{
    public function index()
    {
        $feeds = Feed::with('user')->latest()->get();
        if (!$feeds) {
            return response()->json([
                'status' => false,
                'message' => "Feed not found"
            ], 404);
        }

        return response()->json([
            'status' => true,
            'message' => "Success get feeds",
            'data' => $feeds
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required | string | min:6'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()
            ], 422);
        }

        $user = Auth::user();
        $data = Feed::create([
            'user_id' => $user->id,
            'content' => $request->content
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Success store new feed',
            'data' => $data
        ]);
    }

    public function likePost($feed_id)
    {
        $feed = Feed::find($feed_id);
        if (!$feed) {
            return response()->json([
                'status' => false,
                'message' => 'Feed no found'
            ], 404);
        }

        $likePost = Like::where('user_id', Auth::user()->id)
            ->where('feed_id', $feed_id)
            ->first();

        if ($likePost) {
            $likePost->delete();
            return response()->json([
                'status' => true,
                'message' => "Unliked"
            ]);
        }

        Like::create([
            'user_id' => Auth::user()->id,
            'feed_id' => $feed_id
        ]);

        return response()->json([
            'status' => true,
            'message' => "Liked"
        ]);
    }

    public function comment(Request $request, $feed_id)
    {
        $validator = Validator::make($request->all(), [
            'body' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(
                [
                    'status' => false,
                    'message' => $validator->errors()
                ],
                422
            );
        }

        $comment = Comment::create([
            'user_id' => Auth::user()->id,
            'feed_id' => $feed_id,
            'body' => $request->body
        ]);

        return response()->json(
            [
                'status' => true,
                'message' => "Comment Successful",
                'data' => $comment
            ]
        );
    }

    public function getComment($feed_id)
    {
        $comments = Comment::where('feed_id', $feed_id)->with('user', 'feed')->latest()->get();

        return response()->json(
            [
                'status' => true,
                'message' => "Success get comments",
                'data' => $comments
            ]
        );
    }
}
