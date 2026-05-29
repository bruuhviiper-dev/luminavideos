<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query      = $request->input('q', '');
        $sort       = $request->input('sort', 'relevance'); // relevance|date|views
        $type       = $request->input('type', 'all');       // all|video|short|live
        $duration   = $request->input('duration', 'any');   // any|short|medium|long
        $categoryId = $request->input('category');

        $categories = Category::orderBy('name')->get();

        $videos = Video::where('status', 'active')
            ->where('visibility', 'public')
            ->where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%")
                  ->orWhere('tags', 'LIKE', "%{$query}%");
            });

        if ($type === 'short') {
            $videos->where('is_short', true);
        } elseif ($type === 'video') {
            $videos->where('is_short', false);
        }

        if ($categoryId) {
            $videos->where('category_id', $categoryId);
        }

        // Duration filter
        if ($duration === 'short') {
            $videos->where('duration', '<=', 240); // <4 min
        } elseif ($duration === 'medium') {
            $videos->whereBetween('duration', [240, 1200]); // 4-20 min
        } elseif ($duration === 'long') {
            $videos->where('duration', '>', 1200); // >20 min
        }

        // Sort
        match ($sort) {
            'date'      => $videos->orderByDesc('created_at'),
            'views'     => $videos->orderByDesc('views_count'),
            'rating'    => $videos->orderByDesc('likes_count'),
            default     => $videos->orderByRaw('(CASE WHEN title LIKE ? THEN 3 WHEN description LIKE ? THEN 2 ELSE 1 END) DESC, views_count DESC', ["%{$query}%", "%{$query}%"]),
        };

        $videos = $videos->with('user', 'category')->paginate(20)->withQueryString();

        return view('search', compact('videos', 'query', 'sort', 'type', 'duration', 'categories', 'categoryId'));
    }
}
