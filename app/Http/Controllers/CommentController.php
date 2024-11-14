<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    // In CommentController.php
    public function index($newsId)
    {
        // Fetch the comments for the given news item, with the associated user data
        $comments = Comment::where('news_id', $newsId)
                           ->with('user')  // Eager load the 'user' relationship
                           ->get()
                           ->map(function ($comment) {
                               $comment->user_name = $comment->user->name; // Add user_name
                               return $comment;
                           });

        return response()->json($comments);
    }

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
        
        // Load user data for the created comment
        $comment->load('user');
        
        // Fetch all comments after adding the new comment
        $comments = Comment::where('news_id', $newsId)
                           ->with('user')
                           ->get()
                           ->map(function ($comment) {
                               $comment->user_name = $comment->user->name;
                               return $comment;
                           });

        return response()->json([
            'comment' => $comment,  // Return the newly added comment
            'comments' => $comments,  // Return all comments for the news item
        ], 201);
    }
}
