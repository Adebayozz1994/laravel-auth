<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
      // Fetch all news items with their comments and likes
    public function index()
    {
        // Return news with related comments and likes
        return News::with('comments', 'likes')->get();
    }

    // Store a new news post (only for authenticated admins)
    public function store(Request $request)
    {
        // Check if the admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Validate the incoming request data
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        // Create a new news post
        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => Auth::guard('admin')->user()->name, // Assuming you're using the admin guard
        ]);

        // Return the created news post with a success response
        return response()->json(['news' => $news, 'message' => 'News posted successfully!'], 201);
    }

    // Update an existing news post (only for authenticated admins)
    public function update(Request $request, $id)
    {
        // Check if the admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the news post by ID
        $news = News::findOrFail($id);

        // Update the news post with the provided data
        $news->update($request->only('title', 'content'));

        // Return the updated news post with a success response
        return response()->json(['news' => $news, 'message' => 'News updated successfully!'], 200);
    }

    // Delete a news post (only for authenticated admins)
    public function destroy($id)
    {
        // Check if the admin is authenticated
        if (!Auth::guard('admin')->check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Find the news post by ID
        $news = News::findOrFail($id);

        // Delete the news post
        $news->delete();

        // Return a success message
        return response()->json(['message' => 'News deleted successfully!'], 200);
    }
}
