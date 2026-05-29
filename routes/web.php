<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\VideoController;
use App\Http\Controllers\ChannelController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PlaylistController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\LiveController;
use App\Http\Controllers\MonetizationController;
use App\Http\Controllers\AnalyticsController;
use Illuminate\Support\Facades\Route;

// === Public Routes ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/busca', [SearchController::class, 'search'])->name('search');
Route::get('/categoria/{slug}', [HomeController::class, 'category'])->name('category');
Route::get('/watch', [VideoController::class, 'show'])->name('video.show');
Route::get('/api/watch', [VideoController::class, 'apiShow'])->name('api.video.show');
Route::get('/shorts', [VideoController::class, 'shortsIndex'])->name('shorts.index');
Route::get('/canal/@{username}', [ChannelController::class, 'show'])->name('channel.show');
Route::get('/canal/@{username}/videos', [ChannelController::class, 'videos'])->name('channel.videos');
Route::get('/canal/@{username}/playlists', [ChannelController::class, 'playlists'])->name('channel.playlists');
Route::get('/lives', [LiveController::class, 'index'])->name('live.index');
Route::get('/live/{live}', [LiveController::class, 'show'])->name('live.show');

// Health check
Route::get('/health', [HomeController::class, 'health'])->name('health');

// Webhooks (no auth required)
Route::post('/webhook/mercadopago', [MonetizationController::class, 'webhook'])->name('webhook.mercadopago');
Route::post('/live/webhook', [LiveController::class, 'webhook'])->name('live.webhook');

// === Guest Only Routes ===
Route::middleware(['guest', 'throttle:5,15'])->group(function () {
    Route::get('/login', [HomeController::class, 'login'])->name('login');
    Route::post('/login', [HomeController::class, 'authenticate'])->name('login.store');
    Route::get('/register', [HomeController::class, 'register'])->name('register');
    Route::post('/register', [HomeController::class, 'store'])->name('register.store');
});

// === Authenticated Routes ===
Route::middleware('auth')->group(function () {
    // Auth
    Route::post('/logout', [HomeController::class, 'logout'])->name('logout');

    // Profile & Settings
    Route::get('/configuracoes', [HomeController::class, 'settings'])->name('settings');
    Route::put('/profile', [HomeController::class, 'updateProfile'])->name('profile.update');
    Route::put('/configuracoes/notificacoes', [HomeController::class, 'notificationPrefs'])->name('notifications.prefs');

    // === Videos ===
    Route::get('/upload', [VideoController::class, 'create'])->name('video.create');
    Route::post('/upload', [VideoController::class, 'store'])->middleware('throttle:10,1')->name('video.store');
    Route::get('/video/{id}/edit', [VideoController::class, 'edit'])->name('video.edit');
    Route::put('/video/{id}', [VideoController::class, 'update'])->name('video.update');
    Route::delete('/video/{id}', [VideoController::class, 'destroy'])->name('video.destroy');
    Route::post('/video/{id}/like', [VideoController::class, 'like'])->middleware('throttle:30,1')->name('video.like');
    Route::post('/video/{id}/dislike', [VideoController::class, 'dislike'])->middleware('throttle:30,1')->name('video.dislike');
    Route::post('/video/{id}/report', [VideoController::class, 'report'])->middleware('throttle:5,1')->name('video.report');
    Route::post('/api/track-interaction', [VideoController::class, 'trackInteraction'])->middleware('throttle:60,1')->name('video.track');

    // === Shorts ===
    Route::get('/shorts/criar', [VideoController::class, 'createShort'])->name('shorts.create');

    // === Comments ===
    Route::post('/video/{id}/comment', [CommentController::class, 'store'])->name('comment.store');
    Route::delete('/comment/{id}', [CommentController::class, 'destroy'])->name('comment.destroy');
    Route::post('/comment/{id}/like', [CommentController::class, 'like'])->name('comment.like');

    // === Channel ===
    Route::post('/canal/@{username}/subscribe', [ChannelController::class, 'subscribe'])->name('channel.subscribe');
    Route::delete('/canal/@{username}/unsubscribe', [ChannelController::class, 'unsubscribe'])->name('channel.unsubscribe');

    // === Playlists ===
    Route::get('/playlists', [PlaylistController::class, 'index'])->name('playlist.index');
    Route::get('/playlist/{id}', [PlaylistController::class, 'show'])->name('playlist.show');
    Route::post('/playlist', [PlaylistController::class, 'store'])->name('playlist.store');
    Route::put('/playlist/{id}', [PlaylistController::class, 'update'])->name('playlist.update');
    Route::delete('/playlist/{id}', [PlaylistController::class, 'destroy'])->name('playlist.destroy');
    Route::post('/playlist/{id}/add-video/{videoId}', [PlaylistController::class, 'addVideo'])->name('playlist.add-video');
    Route::delete('/playlist/{id}/remove-video/{videoId}', [PlaylistController::class, 'removeVideo'])->name('playlist.remove-video');

    // === Watch History ===
    Route::get('/historico', [HistoryController::class, 'index'])->name('history.index');
    Route::post('/historico/salvar', [HistoryController::class, 'save'])->name('history.save');
    Route::delete('/historico/limpar', [HistoryController::class, 'clear'])->name('history.clear');

    // === Notifications ===
    Route::get('/notificacoes', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notificacoes/{id}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notificacoes/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    // === Live Streaming ===
    Route::get('/live/criar', [LiveController::class, 'create'])->name('live.create');
    Route::post('/live', [LiveController::class, 'store'])->name('live.store');
    Route::post('/live/{live}/end', [LiveController::class, 'end'])->name('live.end');
    Route::post('/live/{live}/message', [LiveController::class, 'sendMessage'])->name('live.message');
    Route::get('/live/{live}/messages', [LiveController::class, 'getMessages'])->name('live.messages');
    Route::delete('/live/{live}/message/{message}', [LiveController::class, 'deleteMessage'])->name('live.message.delete');
    Route::post('/live/{live}/ban', [LiveController::class, 'banUser'])->name('live.ban');
    Route::post('/live/{live}/viewers', [LiveController::class, 'updateViewers'])->name('live.viewers');

    // === Monetização ===
    Route::get('/studio/monetizacao', [MonetizationController::class, 'dashboard'])->name('monetization.dashboard');
    Route::post('/membership/plan', [MonetizationController::class, 'storePlan'])->name('membership.plan.store');
    Route::post('/membership/{channelUsername}/subscribe', [MonetizationController::class, 'subscribe'])->name('membership.subscribe');
    Route::post('/withdrawal/request', [MonetizationController::class, 'requestWithdrawal'])->name('withdrawal.request');
    Route::delete('/membership/plan/{plan}', [MonetizationController::class, 'deletePlan'])->name('membership.plan.delete');
    Route::delete('/membership/{membership}', [MonetizationController::class, 'cancelMembership'])->name('membership.cancel');
    Route::post('/live/{liveId}/super-chat', [MonetizationController::class, 'sendSuperChat'])->name('superchat.send');
    Route::post('/studio/withdraw', [MonetizationController::class, 'requestWithdrawal'])->name('withdrawal.request.studio');

    // === Analytics ===
    Route::get('/studio/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/studio/analytics/{video}', [AnalyticsController::class, 'videoDetail'])->name('analytics.video');
    Route::post('/api/analytics/interaction', [AnalyticsController::class, 'recordInteraction'])->name('analytics.interaction');
});

// === Admin Routes ===
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/videos', [AdminController::class, 'videos'])->name('admin.videos');
    Route::put('/video/{id}/status', [AdminController::class, 'updateVideoStatus'])->name('admin.video.status');
    Route::delete('/video/{id}', [AdminController::class, 'deleteVideo'])->name('admin.video.delete');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::put('/user/{id}/verify', [AdminController::class, 'verifyUser'])->name('admin.user.verify');
    Route::put('/user/{id}/ban', [AdminController::class, 'banUser'])->name('admin.user.ban');
    Route::get('/categories', [AdminController::class, 'categories'])->name('admin.categories');
    Route::post('/category', [AdminController::class, 'storeCategory'])->name('admin.category.store');
    Route::put('/category/{id}', [AdminController::class, 'updateCategory'])->name('admin.category.update');
    Route::delete('/category/{id}', [AdminController::class, 'deleteCategory'])->name('admin.category.delete');
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
    Route::put('/report/{id}/status', [AdminController::class, 'updateReportStatus'])->name('admin.report.status');
});
