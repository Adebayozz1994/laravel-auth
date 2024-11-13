<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index()
    {
        return News::with('comments', 'likes')->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
        ]);

        $news = News::create([
            'title' => $request->title,
            'content' => $request->content,
            'author' => auth()->user()->name,
        ]);

        return response()->json(['news' => $news], 201);
    }

    public function update(Request $request, $id)
    {
        $news = News::findOrFail($id);

        $news->update($request->only('title', 'content'));

        return response()->json(['news' => $news], 200);
    }

    public function destroy($id)
    {
        $news = News::findOrFail($id);
        $news->delete();

        return response()->json(['message' => 'News deleted'], 200);
    }
}
