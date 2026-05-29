<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ==========================================
        // Índices Compostos para Performance
        // ==========================================

        Schema::table('videos', function (Blueprint $table) {
            // Feed da home: filtrar por status + visibilidade + ordenar por data
            if (!$this->indexExists('videos', 'idx_videos_status_visibility_created'))
                $table->index(['status', 'visibility', 'created_at'], 'idx_videos_status_visibility_created');

            // Studio: vídeos do criador
            if (!$this->indexExists('videos', 'idx_videos_user_status'))
                $table->index(['user_id', 'status', 'created_at'], 'idx_videos_user_status');

            // Filtro por categoria
            if (!$this->indexExists('videos', 'idx_videos_category_status'))
                $table->index(['category_id', 'status', 'visibility'], 'idx_videos_category_status');

            // Busca por slug e youtube_id (já únicos mas garantindo índice)
            // já tem UNIQUE, mas garantindo
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            if (!$this->indexExists('subscriptions', 'idx_subs_subscriber_channel'))
                $table->index(['subscriber_id', 'channel_id'], 'idx_subs_subscriber_channel');

            if (!$this->indexExists('subscriptions', 'idx_subs_channel'))
                $table->index(['channel_id'], 'idx_subs_channel');
        });

        Schema::table('likes', function (Blueprint $table) {
            if (!$this->indexExists('likes', 'idx_likes_video_user'))
                $table->index(['video_id', 'user_id'], 'idx_likes_video_user');
        });

        Schema::table('comments', function (Blueprint $table) {
            if (!$this->indexExists('comments', 'idx_comments_video_created'))
                $table->index(['video_id', 'created_at'], 'idx_comments_video_created');
        });

        Schema::table('watch_histories', function (Blueprint $table) {
            if ($this->tableExists('watch_histories')) {
                if (!$this->indexExists('watch_histories', 'idx_history_user_created'))
                    $table->index(['user_id', 'created_at'], 'idx_history_user_created');
            }
        });

        // ==========================================
        // FULLTEXT search para MySQL
        // ==========================================
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE videos ADD FULLTEXT INDEX idx_videos_fulltext (title, description)');
        }
    }

    public function down(): void
    {
        Schema::table('videos', function (Blueprint $table) {
            try { $table->dropIndex('idx_videos_status_visibility_created'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_videos_user_status'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_videos_category_status'); } catch (\Exception $e) {}
        });

        Schema::table('subscriptions', function (Blueprint $table) {
            try { $table->dropIndex('idx_subs_subscriber_channel'); } catch (\Exception $e) {}
            try { $table->dropIndex('idx_subs_channel'); } catch (\Exception $e) {}
        });

        Schema::table('likes', function (Blueprint $table) {
            try { $table->dropIndex('idx_likes_video_user'); } catch (\Exception $e) {}
        });

        Schema::table('comments', function (Blueprint $table) {
            try { $table->dropIndex('idx_comments_video_created'); } catch (\Exception $e) {}
        });
    }

    private function indexExists(string $table, string $indexName): bool
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            $indexes = DB::select("PRAGMA index_list({$table})");
            foreach ($indexes as $idx) {
                if ($idx->name === $indexName) return true;
            }
            return false;
        }

        // MySQL
        $indexes = DB::select("SHOW INDEX FROM {$table} WHERE Key_name = ?", [$indexName]);
        return count($indexes) > 0;
    }

    private function tableExists(string $table): bool
    {
        return Schema::hasTable($table);
    }
};
