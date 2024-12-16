<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
        return News::withCount(['comments', 'likes'])
            ->get()
            ->map(function ($news) {
                $news->likeCount = $news->likes_count;
                $news->commentCount = $news->comments_count;
                $news->image_url = $news->image_path ? asset('storage/' . $news->image_path) : null; // Add the full image URL
                return $news;
            });
    }
    

    // Store a new news post (with image upload)
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('news_images', 'public');
        }

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => Auth::guard('admin')->user()->name,
            'image_path' => $imagePath,
        ]);

        return response()->json(['news' => $news, 'message' => 'News posted successfully!'], 201);
    }

    // Update an existing news post (with image upload)
    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete the old image if it exists
            if ($news->image_path) {
                Storage::disk('public')->delete($news->image_path);
            }
            $news->image_path = $request->file('image')->store('news_images', 'public');
        }

        $news->update([
            'title' => $request->title,
            'content' => $request->content,
        ]);

        return response()->json(['news' => $news, 'message' => 'News updated successfully!'], 200);
    }

    // Delete a news post
    public function destroy($id)
    {
        $news = News::findOrFail($id);

        // Delete the associated image if it exists
        if ($news->image_path) {
            Storage::disk('public')->delete($news->image_path);
        }

        $news->delete();

        return response()->json(['message' => 'News deleted successfully!'], 200);
    }
}