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
        // Ensure the user is logged in
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
    
        // Load the user data for the newly created comment
        $comment->load('user');
    
        // Add the user_name to the response
        $comment->user_name = $comment->user->name;
    
        // Return the new comment with the user_name
        return response()->json([
            'comment' => $comment, // Newly added comment with user_name
        ], 201);
    }
    
}
