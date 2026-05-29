<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Report;
use App\Models\User;
use App\Models\Video;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->is_admin) {
                abort(403);
            }
            return $next($request);
        });
    }

    public function dashboard()
    {
        $totalUsers = User::count();
        $totalVideos = Video::count();
        $totalComments = Comment::count() ?? 0;
        $pendingReports = Report::where('status', 'pending')->count();
        $categories = Category::all();
        
        return view('admin.dashboard', compact('totalUsers', 'totalVideos', 'totalComments', 'pendingReports', 'categories'));
    }

    public function videos()
    {
        $videos = Video::with('user', 'category')->paginate(20);
        $categories = Category::all();
        return view('admin.videos', compact('videos', 'categories'));
    }

    public function updateVideoStatus(Request $request, $id)
    {
        $video = Video::findOrFail($id);
        $validated = $request->validate(['status' => 'required|in:processing,active,blocked']);
        $video->update($validated);
        
        return back()->with('status', 'Status do vídeo atualizado!');
    }

    public function deleteVideo($id)
    {
        Video::findOrFail($id)->delete();
        return back()->with('status', 'Vídeo deletado!');
    }

    public function users()
    {
        $users = User::paginate(20);
        $categories = Category::all();
        return view('admin.users', compact('users', 'categories'));
    }

    public function verifyUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_verified' => true]);
        return back()->with('status', 'Usuário verificado!');
    }

    public function banUser($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_admin' => false]);
        return back()->with('status', 'Usuário banido!');
    }

    public function categories()
    {
        $categories = Category::all();
        $allCategories = Category::all();
        return view('admin.categories', compact('categories', 'allCategories'));
    }

    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:categories',
            'slug' => 'required|string|unique:categories',
            'icon' => 'nullable|string',
        ]);
        
        Category::create($validated);
        return back()->with('status', 'Categoria criada!');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name' => 'string|unique:categories,name,' . $id,
            'slug' => 'string|unique:categories,slug,' . $id,
            'icon' => 'nullable|string',
        ]);
        
        $category->update($validated);
        return back()->with('status', 'Categoria atualizada!');
    }

    public function deleteCategory($id)
    {
        Category::findOrFail($id)->delete();
        return back()->with('status', 'Categoria deletada!');
    }

    public function reports()
    {
        $reports = Report::with('user', 'video')->orderBy('created_at', 'desc')->paginate(20);
        $categories = Category::all();
        return view('admin.reports', compact('reports', 'categories'));
    }

    public function updateReportStatus(Request $request, $id)
    {
        $report = Report::findOrFail($id);
        $validated = $request->validate(['status' => 'required|in:pending,reviewed,resolved']);
        $report->update($validated);
        
        return back()->with('status', 'Status da denúncia atualizado!');
    }
}
