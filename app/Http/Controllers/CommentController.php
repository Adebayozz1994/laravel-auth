<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    public function store(Request $request, $newsId)
    {
        // Ensure the user is logged in before continuing
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the comment input
        $request->validate(['comment' => 'required|string']);

        // Create the comment using the logged-in user's ID
        $comment = Comment::create([
            'news_id' => $newsId,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['comment' => $comment], 201);
    }
}
