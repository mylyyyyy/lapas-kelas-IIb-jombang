<?php

namespace App\Http\Controllers;

use App\Models\News;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    /**
     * Display a listing of the news.
     */
    public function index(Request $request)
    {
        $query = News::where('status', 'published');

        // Search functionality
        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', '%' . $search . '%')
                  ->orWhere('content', 'like', '%' . $search . '%');
            });
        }

        // Category filter (assuming there's a category field, or use tags/content)
        if ($request->has('category') && !empty($request->category)) {
            $category = $request->category;
            $query->where('content', 'like', '%' . $category . '%'); // Simple filter, can be improved
        }

        $orderColumn = \Illuminate\Support\Facades\Schema::hasColumn('news', 'published_at') ? 'published_at' : 'created_at';
        $allNews = $query->orderBy($orderColumn, 'desc')->paginate(10)->appends($request->query());
        return view('news.index', compact('allNews'));
    }

    /**
     * Display the specified news.
     */
    public function show(News $news)
    {
        // Ensure only published news can be viewed publicly
        if ($news->status !== 'published') {
            abort(404);
        }

        $orderColumn = \Illuminate\Support\Facades\Schema::hasColumn('news', 'published_at') ? 'published_at' : 'created_at';

        // Get previous and next news articles
        $previousNews = News::where('status', 'published')
            ->where($orderColumn, '<', $news->$orderColumn)
            ->orderBy($orderColumn, 'desc')
            ->first();

        $nextNews = News::where('status', 'published')
            ->where($orderColumn, '>', $news->$orderColumn)
            ->orderBy($orderColumn, 'asc')
            ->first();

        return view('news.show', compact('news', 'previousNews', 'nextNews'));
    }
}
