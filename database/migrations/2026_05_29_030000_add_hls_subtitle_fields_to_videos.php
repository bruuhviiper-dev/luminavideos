<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'hls_path')) {
                $table->string('hls_path')->nullable()->after('video_path');
            }
            if (!Schema::hasColumn('videos', 'subtitle_srt_path')) {
                $table->string('subtitle_srt_path')->nullable()->after('hls_path');
            }
            if (!Schema::hasColumn('videos', 'subtitle_vtt_path')) {
                $table->string('subtitle_vtt_path')->nullable()->after('subtitle_srt_path');
            }
            if (!Schema::hasColumn('videos', 'subtitle_language')) {
                $table->string('subtitle_language')->nullable()->after('subtitle_vtt_path');
            }
            if (!Schema::hasColumn('videos', 'is_short')) {
                $table->boolean('is_short')->default(false)->after('subtitle_language');
            }
            if (!Schema::hasColumn('videos', 'hls_360p_path')) {
                $table->string('hls_360p_path')->nullable()->after('hls_path');
            }
            if (!Schema::hasColumn('videos', 'hls_720p_path')) {
                $table->string('hls_720p_path')->nullable()->after('hls_360p_path');
            }
            if (!Schema::hasColumn('videos', 'hls_1080p_path')) {
                $table->string('hls_1080p_path')->nullable()->after('hls_720p_path');
            }
        });
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            $table->dropColumn([
                'hls_path', 'hls_360p_path', 'hls_720p_path', 'hls_1080p_path',
                'subtitle_srt_path', 'subtitle_vtt_path', 'subtitle_language', 'is_short'
            ]);
        });
    }
};
