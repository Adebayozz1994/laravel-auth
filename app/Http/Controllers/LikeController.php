<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LikeController extends Controller
{
    public function toggleLike($newsId)
    {
        if (!Auth::check()) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = Auth::id();

        $like = Like::where('news_id', $newsId)
                    ->where('user_id', $userId)
                    ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false], 200);
        } else {
            Like::create(['news_id' => $newsId, 'user_id' => $userId]);
            return response()->json(['liked' => true], 200);
        }
    }
}
