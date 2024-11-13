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
           return News::with('comments', 'likes')->get();
       }
   
       // Store a new news post (only for authenticated users)
       public function store(Request $request)
       {
           if (!Auth::check()) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }
   
           $request->validate([
               'title' => 'required|string|max:255',
               'content' => 'required|string',
           ]);
   
           $news = News::create([
               'title' => $request->title,
               'content' => $request->content,
               'author' => Auth::user()->name,
           ]);
   
           return response()->json(['news' => $news], 201);
       }
   
       // Update an existing news post (only for authenticated users)
       public function update(Request $request, $id)
       {
           if (!Auth::check()) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }
   
           $news = News::findOrFail($id);
   
           $news->update($request->only('title', 'content'));
   
           return response()->json(['news' => $news], 200);
       }
   
       // Delete a news post (only for authenticated users)
       public function destroy($id)
       {
           if (!Auth::check()) {
               return response()->json(['error' => 'Unauthorized'], 401);
           }
   
           $news = News::findOrFail($id);
           $news->delete();
   
           return response()->json(['message' => 'News deleted'], 200);
       }
}
