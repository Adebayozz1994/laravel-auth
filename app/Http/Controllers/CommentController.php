<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function store(Request $request, $newsId)
    {
        $request->validate(['comment' => 'required|string']);

        $comment = Comment::create([
            'news_id' => $newsId,
            'user_id' => auth()->id(),
            'comment' => $request->comment,
        ]);

        return response()->json(['comment' => $comment], 201);
    }
}
