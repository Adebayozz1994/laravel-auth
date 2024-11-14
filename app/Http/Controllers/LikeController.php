<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
        public function toggleLike($newsId)
    {
        // Ensure the user is authenticated
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();
        
        // Find the news post by its ID
        $news = News::findOrFail($newsId);
        
        // Check if the user has already liked the news post
        $like = Like::where('news_id', $newsId)
                    ->where('user_id', $userId)
                    ->first();

        if ($like) {
            // User has already liked the news, so remove the like
            $like->delete();
            $liked = false;
        } else {
            // User has not liked the news, so create a new like
            Like::create(['news_id' => $newsId, 'user_id' => $userId]);
            $liked = true;
        }

        // Get the updated like count for the news
        $likeCount = $news->likes->count();

        // Return the updated like status and like count
        return response()->json([
            'liked' => $liked,
            'likeCount' => $likeCount,  // Return the updated like count
        ], 200);
    }
    
}
