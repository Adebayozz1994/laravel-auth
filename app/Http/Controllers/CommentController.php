<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
   // In CommentController.php
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
    $comment->load('user');
    // Retrieve all comments for the news post, including user information
    $comments = Comment::where('news_id', $newsId)
    ->with('user')  // Eager load the user relationship
    ->get()
    ->map(function ($comment) {
        // Use the 'name' field for the full name
        $comment->user_name = $comment->user->name;
        return $comment;
    });

     // Corrected part here: Get the current user's name
     $currentUserName = Auth::user()->name;

     return response()->json([
        'comment' => $comment,
        'comments' => $comments,
        'user_name' => Auth::user()->name,  // Correct way to get the user's name
    ], 201);
}
}
