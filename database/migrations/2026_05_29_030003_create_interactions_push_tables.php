<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('video_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('video_id')->constrained()->onDelete('cascade');
            $table->enum('action', ['view', 'like', 'dislike', 'share', 'comment', 'skip']);
            $table->unsignedTinyInteger('watch_percentage')->default(0);
            $table->string('session_id')->nullable();
            $table->string('traffic_source')->nullable(); // search/recommended/direct/channel/external
            $table->timestamp('created_at')->useCurrent();
            
            $table->index(['video_id', 'action']);
            $table->index(['user_id', 'video_id']);
        });

        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('endpoint');
            $table->text('public_key');
            $table->text('auth_token');
            $table->timestamps();
            
            $table->unique(['user_id', 'endpoint'], 'push_user_endpoint_unique');
        });

        Schema::create('notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->boolean('push_enabled')->default(true);
            $table->boolean('email_new_videos')->default(true);
            $table->boolean('email_lives')->default(true);
            $table->boolean('email_weekly_digest')->default(true);
            $table->boolean('email_comments')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_preferences');
        Schema::dropIfExists('push_subscriptions');
        Schema::dropIfExists('video_interactions');
    }
};
