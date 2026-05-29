<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Performance indexes for videos table
        Schema::table('videos', function (Blueprint $table) {
            $table->index(['status', 'visibility', 'created_at'], 'videos_status_visibility_created_idx');
            $table->index(['user_id', 'status'], 'videos_user_status_idx');
            $table->index(['category_id', 'status'], 'videos_category_status_idx');
            $table->index('views_count', 'videos_views_idx');
            $table->index('is_short', 'videos_is_short_idx');
        });

        // Performance indexes for comments table
        Schema::table('comments', function (Blueprint $table) {
            $table->index(['video_id', 'created_at'], 'comments_video_created_idx');
            $table->index('user_id', 'comments_user_idx');
        });

        // Performance indexes for likes table
        Schema::table('likes', function (Blueprint $table) {
            $table->index(['user_id', 'video_id'], 'likes_user_video_idx');
        });

        // Performance indexes for subscriptions table
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->index(['subscriber_id', 'channel_id'], 'subs_subscriber_channel_idx');
        });

        // Performance indexes for watch_history table
        Schema::table('watch_history', function (Blueprint $table) {
            $table->index(['user_id', 'created_at'], 'watch_history_user_created_idx');
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropIndex('videos_status_visibility_created_idx');
            $table->dropIndex('videos_user_status_idx');
            $table->dropIndex('videos_category_status_idx');
            $table->dropIndex('videos_views_idx');
            $table->dropIndex('videos_is_short_idx');
        });

        Schema::table('comments', function (Blueprint $table) {
            $table->dropIndex('comments_video_created_idx');
            $table->dropIndex('comments_user_idx');
        });

        Schema::table('likes', function (Blueprint $table) {
            $table->dropIndex('likes_user_video_idx');
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex('subs_subscriber_channel_idx');
        });

        Schema::table('watch_history', function (Blueprint $table) {
            $table->dropIndex('watch_history_user_created_idx');
        });
    }
};
