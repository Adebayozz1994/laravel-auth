<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ExternalNews;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ExternalNewsController extends Controller
{
    public function fetchExternalNews()
    {
        $response = Http::get('https://newsapi.org/v2/top-headlines', [
            'apiKey' => '88c58969e7ed4137b87c6cf97e2af9b2',
            'country' => 'us',  // Fetch news from the US
        ]);

        Log::info('NewsAPI Response:', $response->json());

        if ($response->successful()) {
            $articles = $response->json()['articles'];

            if (empty($articles)) {
                Log::warning('No articles found');
                return response()->json(['status' => 'error', 'message' => 'No articles found']);
            }

            foreach ($articles as $article) {
                // Avoid saving duplicate news articles
                if (!ExternalNews::where('url', $article['url'])->exists()) {
                    try {
                        ExternalNews::create([
                            'source_id' => $article['source']['id'] ?? null,
                            'source_name' => $article['source']['name'],
                            'author' => $article['author'] ?? null,
                            'title' => $article['title'],
                            'description' => $article['description'],
                            'url' => $article['url'],
                            'url_to_image' => $article['urlToImage'] ?? null,
                            'published_at' => Carbon::parse($article['publishedAt']),
                            'content' => $article['content'] ?? null,
                        ]);
                    } catch (\Exception $e) {
                        Log::error('Error saving external news: ' . $e->getMessage());
                    }
                }
            }

            return response()->json(ExternalNews::all());  // Return saved external news
        } else {
            Log::error('Failed to fetch news from API. Status: ' . $response->status());
            return response()->json([
                'status' => 'error',
                'message' => 'Failed to fetch news',
            ], 500);
        }
    }

    // Get all external news
    public function getExternalNews()
    {
        $externalNews = ExternalNews::all();  // Fetch all news without comments
        return response()->json($externalNews);
    }

   
}