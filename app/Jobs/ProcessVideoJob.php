<?php

namespace App\Jobs;

use App\Models\Video;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Storage;

class ProcessVideoJob implements ShouldQueue
{
    use Queueable;

    public function __construct(private Video $video)
    {
        //
    }

    public function handle(): void
    {
        try {
            // Get video path
            $videoPath = Storage::disk('public')->path($this->video->video_path);
            
            // Extract video duration (simplified - would use FFMpeg in production)
            $duration = $this->getVideoDuration($videoPath);
            $this->video->update(['duration' => $duration]);
            
            // Generate thumbnail
            $thumbnailPath = $this->generateThumbnail($videoPath);
            $this->video->update(['thumbnail' => $thumbnailPath]);
            
            // Update status to active
            $this->video->update(['status' => 'active']);
        } catch (\Exception $e) {
            $this->video->update(['status' => 'blocked']);
        }
    }

    private function getVideoDuration($videoPath): int
    {
        // Simplified duration extraction
        return 600; // 10 minutes default
    }

    private function generateThumbnail($videoPath): string
    {
        // Simplified thumbnail generation
        return 'thumbnails/default.jpg';
    }
}

