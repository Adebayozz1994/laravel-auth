<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;

class LikeController extends Controller
{
    public function toggleLike($newsId)
    {
        $like = Like::where('news_id', $newsId)
                    ->where('user_id', auth()->id())
                    ->first();

        if ($like) {
            $like->delete();
            return response()->json(['liked' => false], 200);
        } else {
            Like::create(['news_id' => $newsId, 'user_id' => auth()->id()]);
            return response()->json(['liked' => true], 200);
        }
    }
}
