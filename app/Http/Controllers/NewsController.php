<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    public function __construct()
    {
        // Check if the admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }

    // Fetch all news items with their comments and likes
    public function index()
    {
        return News::with(['comments.user', 'likes'])->get();
    }

    // Store a new news post (only for authenticated admins)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => Auth::guard('admin')->user()->name,
        ]);

        return response()->json(['news' => $news, 'message' => 'News posted successfully!'], 201);
    }

    // Update an existing news post (only for authenticated admins)
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $news->update($request->only('title', 'content'));

        return response()->json(['news' => $news, 'message' => 'News updated successfully!'], 200);
    }

    // Delete a news post (only for authenticated admins)
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        $news->delete();

        return response()->json(['message' => 'News deleted successfully!'], 200);
    }
}
