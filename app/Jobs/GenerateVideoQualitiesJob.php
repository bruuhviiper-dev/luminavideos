<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use FFMpeg\FFMpeg;
use FFMpeg\Format\Video\X264;
use FFMpeg\Coordinate\Dimension;
use FFMpeg\Filters\Video\VideoFilters;

class GenerateVideoQualitiesJob implements ShouldQueue
{
    use Queueable;

    public int $timeout = 3600; // 1 hour max
    public int $tries = 2;

    private array $qualities = [
        '360p'  => ['width' => 640,  'height' => 360,  'bitrate' => 800,  'audioBitrate' => 96],
        '720p'  => ['width' => 1280, 'height' => 720,  'bitrate' => 2500, 'audioBitrate' => 128],
        '1080p' => ['width' => 1920, 'height' => 1080, 'bitrate' => 5000, 'audioBitrate' => 192],
    ];

    public function __construct(private Video $video) {}

    public function handle(): void
    {
        $videoPath = Storage::disk('public')->path($this->video->video_path);

        if (!file_exists($videoPath)) {
            Log::error("Video file not found: {$videoPath}");
            return;
        }

        $outputDir = Storage::disk('public')->path("videos/{$this->video->id}/hls");

        if (!is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $this->generateHLSWithFFmpegBinary($videoPath, $outputDir);
    }

    private function generateHLSWithFFmpegBinary(string $inputPath, string $outputDir): void
    {
        $ffmpegPath = $this->findFFmpegBinary();
        $generatedQualities = [];

        foreach ($this->qualities as $name => $config) {
            $qualityDir = "{$outputDir}/{$name}";
            if (!is_dir($qualityDir)) {
                mkdir($qualityDir, 0755, true);
            }

            $playlistPath = "{$qualityDir}/playlist.m3u8";
            $segmentPattern = "{$qualityDir}/segment%03d.ts";

            $cmd = sprintf(
                '"%s" -i "%s" -vf "scale=%d:%d:force_original_aspect_ratio=decrease,pad=%d:%d:(ow-iw)/2:(oh-ih)/2" '
                . '-c:v libx264 -b:v %dk -c:a aac -b:a %dk '
                . '-f hls -hls_time 10 -hls_list_size 0 -hls_segment_filename "%s" "%s" -y 2>&1',
                $ffmpegPath,
                $inputPath,
                $config['width'], $config['height'],
                $config['width'], $config['height'],
                $config['bitrate'],
                $config['audioBitrate'],
                $segmentPattern,
                $playlistPath
            );

            exec($cmd, $output, $returnCode);

            if ($returnCode === 0 || file_exists($playlistPath)) {
                $relativePath = "videos/{$this->video->id}/hls/{$name}/playlist.m3u8";
                $generatedQualities[$name] = $relativePath;
                Log::info("Generated {$name} HLS for video {$this->video->id}");
            } else {
                Log::warning("Failed to generate {$name} for video {$this->video->id}: " . implode("\n", $output));
            }
        }

        if (!empty($generatedQualities)) {
            $this->generateMasterPlaylist($outputDir, $generatedQualities);
        }
    }

    private function generateMasterPlaylist(string $outputDir, array $qualities): void
    {
        $masterContent = "#EXTM3U\n#EXT-X-VERSION:3\n\n";

        $bandwidthMap = [
            '360p'  => 800000,
            '720p'  => 2500000,
            '1080p' => 5000000,
        ];

        $resolutionMap = [
            '360p'  => '640x360',
            '720p'  => '1280x720',
            '1080p' => '1920x1080',
        ];

        foreach ($qualities as $name => $path) {
            $bandwidth = $bandwidthMap[$name] ?? 1000000;
            $resolution = $resolutionMap[$name] ?? '1280x720';
            $segmentName = basename(dirname($path)) . '/playlist.m3u8';

            $masterContent .= "#EXT-X-STREAM-INF:BANDWIDTH={$bandwidth},RESOLUTION={$resolution},NAME=\"{$name}\"\n";
            $masterContent .= "{$name}/playlist.m3u8\n\n";
        }

        $masterPath = "{$outputDir}/master.m3u8";
        file_put_contents($masterPath, $masterContent);

        $relativeMasterPath = "videos/{$this->video->id}/hls/master.m3u8";

        $updateData = ['hls_path' => $relativeMasterPath];

        foreach ($qualities as $name => $path) {
            $field = "hls_{$name}_path";
            $updateData[$field] = $path;
        }

        $this->video->update($updateData);

        Log::info("Master HLS playlist created for video {$this->video->id}");
    }

    private function findFFmpegBinary(): string
    {
        // Try common paths
        $paths = [
            'ffmpeg',
            'C:\\ffmpeg\\bin\\ffmpeg.exe',
            'C:\\Program Files\\ffmpeg\\bin\\ffmpeg.exe',
            '/usr/bin/ffmpeg',
            '/usr/local/bin/ffmpeg',
        ];

        foreach ($paths as $path) {
            exec("\"{$path}\" -version 2>&1", $output, $code);
            if ($code === 0) {
                return $path;
            }
        }

        return 'ffmpeg'; // fallback
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("GenerateVideoQualitiesJob failed for video {$this->video->id}: " . $exception->getMessage());
        $this->video->update(['status' => 'active']); // Don't block video, HLS is optional
    }
}
