<?php

namespace Database\Seeders;

use App\Models\Video;
use Illuminate\Database\Seeder;

class FixVideoUrlsSeeder extends Seeder
{
    private array $urls = [
        'https://vjs.zencdn.net/v/oceans.mp4',
        'https://media.w3.org/2010/05/sintel/trailer_hd.mp4',
        'https://media.w3.org/2010/05/bunny/trailer.mp4',
        'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
        'https://media.w3.org/2010/05/video/movie_300.mp4',
        'https://media.w3.org/2010/05/bunny/movie.mp4',
        'https://www.w3schools.com/html/mov_bbb.mp4',
        'https://www.w3schools.com/html/movie.mp4',
    ];

    private array $thumbs = [
        'https://peach.blender.org/wp-content/uploads/title_anouncement.jpg',
        'https://orange.blender.org/wp-content/uploads/elephants-dream-blog-post.jpg',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/Subaru_Outback_On_Street_And_Dirt.jpg',
        'https://mango.blender.org/wp-content/uploads/2013/05/04_forest_call_04.jpg',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerBlazes.jpg',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerJoyrides.jpg',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/ForBiggerEscapes.jpg',
        'https://commondatastorage.googleapis.com/gtv-videos-bucket/sample/images/WeAreGoingOnBullrun.jpg',
    ];

    private array $durations = [596, 653, 213, 734, 15, 16, 15, 612];

    public function run(): void
    {
        $videos = Video::all();

        foreach ($videos as $index => $video) {
            $i = $index % count($this->urls);
            $video->update([
                'video_url' => $this->urls[$i],
                'thumbnail' => $video->thumbnail ?? $this->thumbs[$i],
                'status'    => 'active',
                'duration'  => $this->durations[$i],
            ]);
        }

        $this->command->info('✅ ' . $videos->count() . ' vídeos corrigidos com URLs reais.');
    }
}
