<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;

class GenerateSubtitlesJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 1800; // 30 minutes
    public int $tries = 2;

    public function __construct(private Video $video) {}

    public function handle(): void
    {
        $videoPath = Storage::disk('public')->path($this->video->video_path);

        if (!file_exists($videoPath)) {
            Log::error("Video file not found for subtitles: {$videoPath}");
            return;
        }

        $scriptPath = base_path('scripts/generate_subtitles.py');

        if (!file_exists($scriptPath)) {
            Log::warning('Whisper subtitle script not found at: ' . $scriptPath);
            return;
        }

        $outputDir = Storage::disk('public')->path("subtitles/{$this->video->id}");

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        try {
            $result = Process::timeout(1800)->run(
                sprintf(
                    'python "%s" --video_path="%s" --video_id=%d --output_dir="%s"',
                    $scriptPath,
                    $videoPath,
                    $this->video->id,
                    $outputDir
                )
            );

            if ($result->successful()) {
                $output = json_decode($result->output(), true);

                if (isset($output['srt_path'], $output['vtt_path'])) {
                    $this->video->update([
                        'subtitle_srt_path' => "subtitles/{$this->video->id}/" . basename($output['srt_path']),
                        'subtitle_vtt_path' => "subtitles/{$this->video->id}/" . basename($output['vtt_path']),
                        'subtitle_language' => $output['language'] ?? 'pt',
                    ]);

                    Log::info("Subtitles generated for video {$this->video->id}: " . $output['language']);
                }
            } else {
                Log::warning("Whisper script failed for video {$this->video->id}: " . $result->errorOutput());
            }
        } catch (\Exception $e) {
            Log::error("GenerateSubtitlesJob error: " . $e->getMessage());
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateSubtitlesJob failed for video {$this->video->id}: " . $exception->getMessage());
    }
}
