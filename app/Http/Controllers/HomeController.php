<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Video;
use App\Services\CacheService;
use App\Services\RecommendationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    public function index()
    {
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::orderBy('name')->get());

        // Call Python AI Engine
        $aiVideos = collect();
        try {
            $userId = Auth::id() ?? 0;
            $scriptPath = base_path('scripts/ai_engine.py');
            if (file_exists($scriptPath)) {
                $process = \Illuminate\Support\Facades\Process::run("python {$scriptPath} {$userId}");
                if ($process->successful()) {
                    $recommendedIds = json_decode($process->output(), true);
                    if (is_array($recommendedIds) && count($recommendedIds) > 0) {
                        $idsOrdered = implode(',', $recommendedIds);
                        $aiVideos = Video::whereIn('id', $recommendedIds)
                            ->where('is_short', false)
                            ->with('user', 'category')
                            ->orderByRaw("FIELD(id, {$idsOrdered})")
                            ->paginate(24);
                    }
                }
            }
        } catch (\Exception $e) {
            \Log::error('AI Engine Error: ' . $e->getMessage());
        }

        if ($aiVideos->isEmpty() && !$aiVideos instanceof \Illuminate\Pagination\LengthAwarePaginator) {
            // Fallback
            $videos = CacheService::remember(CacheService::homeFeedKey(), 10, fn() =>
                Video::where('status', 'active')
                    ->where('visibility', 'public')
                    ->where('is_short', false)
                    ->with('user', 'category')
                    ->orderByDesc('created_at')
                    ->paginate(24)
            );
        } else {
            $videos = $aiVideos;
        }

        $recommended = collect();

        return view('home', compact('videos', 'categories', 'recommended'));
    }

    public function category($slug)
    {
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::orderBy('name')->get());
        $category   = $categories->firstWhere('slug', $slug) ?? Category::where('slug', $slug)->firstOrFail();

        $videos = Video::where('category_id', $category->id)
            ->where('status', 'active')
            ->where('visibility', 'public')
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(24);

        return view('category', compact('category', 'videos', 'categories'));
    }

    public function login()
    {
        return view('auth.login');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors(['email' => 'Credenciais inválidas.'])->withInput($request->only('email'));
    }

    public function register()
    {
        return view('auth.register');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => 'required|string|max:255',
            'username'              => 'required|string|max:50|unique:users|alpha_dash',
            'email'                 => 'required|email|max:255|unique:users',
            'password'              => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required',
        ]);

        $user = \App\Models\User::create([
            'name'     => $validated['name'],
            'username' => $validated['username'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        // Create default notification preferences
        \App\Models\NotificationPreference::create(['user_id' => $user->id]);

        return redirect('/')->with('status', 'Bem-vindo ao Tubiii, ' . $user->name . '! 🎉');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function settings()
    {
        $user = Auth::user();
        $notifPrefs = \App\Models\NotificationPreference::firstOrCreate(['user_id' => $user->id]);
        $categories = CacheService::remember(CacheService::categoriesKey(), 60, fn() => Category::all());
        return view('settings', compact('user', 'notifPrefs', 'categories'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name'    => 'required|string|max:255',
            'bio'     => 'nullable|string|max:500',
            'website' => 'nullable|url|max:255',
            'avatar'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'banner'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        if ($request->hasFile('avatar')) {
            if ($user->avatar) \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            $validated['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->hasFile('banner')) {
            if ($user->banner) \Illuminate\Support\Facades\Storage::disk('public')->delete($user->banner);
            $validated['banner'] = $request->file('banner')->store('banners', 'public');
        }

        $user->update($validated);
        CacheService::invalidateChannel($user->username);

        return back()->with('status', 'Perfil atualizado com sucesso!');
    }

    public function notificationPrefs(Request $request)
    {
        $prefs = \App\Models\NotificationPreference::firstOrCreate(['user_id' => Auth::id()]);
        $prefs->update([
            'push_enabled'       => $request->boolean('push_enabled'),
            'email_new_videos'   => $request->boolean('email_new_videos'),
            'email_lives'        => $request->boolean('email_lives'),
            'email_weekly_digest'=> $request->boolean('email_weekly_digest'),
            'email_comments'     => $request->boolean('email_comments'),
        ]);
        return back()->with('status', 'Preferências salvas!');
    }

    public function health()
    {
        $status = ['status' => 'ok', 'timestamp' => now()->toISOString()];

        // DB check
        try {
            \DB::select('SELECT 1');
            $status['database'] = 'ok';
        } catch (\Exception $e) {
            $status['database'] = 'error: ' . $e->getMessage();
            $status['status'] = 'degraded';
        }

        // Cache check
        try {
            cache()->put('health_check', 'ok', 5);
            $status['cache'] = cache()->get('health_check') === 'ok' ? 'ok' : 'error';
        } catch (\Exception $e) {
            $status['cache'] = 'error';
        }

        // Storage check
        $status['storage'] = is_writable(storage_path('app/public')) ? 'ok' : 'error';

        $httpCode = $status['status'] === 'ok' ? 200 : 503;
        return response()->json($status, $httpCode);
    }
}
