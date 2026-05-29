<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Video;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Criar Categorias Premium
        $cats = [
            ['name' => 'Tecnologia', 'slug' => 'tecnologia', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>'],
            ['name' => 'Filmes e Animação', 'slug' => 'filmes', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4v16M17 4v16M3 8h4m10 0h4M3 12h18M3 16h4m10 0h4M4 20h16a1 1 0 001-1V5a1 1 0 00-1-1H4a1 1 0 00-1 1v14a1 1 0 001 1z" /></svg>'],
            ['name' => 'Games', 'slug' => 'games', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
            ['name' => 'Música', 'slug' => 'musica', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19V6l12-3v13M9 19c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zm12-3c0 1.105-1.343 2-3 2s-3-.895-3-2 1.343-2 3-2 3 .895 3 2zM9 10l12-3" /></svg>'],
            ['name' => 'Vlogs', 'slug' => 'vlogs', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>'],
            ['name' => 'Educação', 'slug' => 'educacao', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222" /></svg>'],
            ['name' => 'Comédia', 'slug' => 'comedia', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>'],
            ['name' => 'Esportes', 'slug' => 'esportes', 'icon' => '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM12 12a3 3 0 100-6 3 3 0 000 6zm0 0a3 3 0 100 6 3 3 0 000-6zm0 0V3m0 9h9m-9 0H3m9 0v9" /></svg>'],
        ];

        $createdCats = [];
        foreach ($cats as $cat) {
            $createdCats[$cat['slug']] = Category::create($cat);
        }

        // 2. Criar Admin Default
        $admin = User::create([
            'name' => 'Admin Lumina',
            'email' => 'admin@lumina.com',
            'username' => 'admin',
            'password' => Hash::make('12345678'),
            'bio' => 'Canal oficial de testes da plataforma Lumina.',
            'avatar' => null, 
            'banner' => null,
            'is_verified' => true,
            'subscribers_count' => rand(100, 500)
        ]);

        // 3. Criar Creators (Usuários Famosos Fakes)
        $creators = [];
        $creators[] = User::create([
            'name' => 'Blender Foundation',
            'username' => 'blender',
            'email' => 'blender@lumina.com',
            'password' => Hash::make('12345678'),
            'bio' => 'Animações Open Source da Blender Foundation.',
            'is_verified' => true,
            'subscribers_count' => rand(5000, 15000)
        ]);
        
        $creators[] = User::create([
            'name' => 'Tech Explorer',
            'username' => 'techexplorer',
            'email' => 'tech@lumina.com',
            'password' => Hash::make('12345678'),
            'bio' => 'Reviews e Vlogs sobre tecnologia.',
            'subscribers_count' => rand(1000, 5000)
        ]);

        $creators[] = User::create([
            'name' => 'GamePlays BR',
            'username' => 'gameplaysbr',
            'email' => 'game@lumina.com',
            'password' => Hash::make('12345678'),
            'bio' => 'O melhor dos jogos competitivos e casuais.',
            'subscribers_count' => rand(500, 2000)
        ]);

        // 4. Inserir Vídeos Reais (CDN) com YouTube ID e Views Counts
        $realVideos = [
            [
                'user_id' => $creators[0]->id,
                'category_id' => $createdCats['filmes']->id,
                'title' => 'Big Buck Bunny (60fps 4K - Official Blender Foundation)',
                'slug' => 'big-buck-bunny-oficial',
                'description' => "Big Buck Bunny tells the story of a giant rabbit with a heart bigger than himself.\n\nCreated by Blender Foundation in 2008.",
                'video_url' => 'https://vjs.zencdn.net/v/oceans.mp4',
                'thumbnail_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c5/Big_buck_bunny_poster_big.jpg/1200px-Big_buck_bunny_poster_big.jpg',
                'tags' => 'animation,blender,movie,bunny',
                'views_count' => rand(100000, 500000),
                'likes_count' => rand(5000, 10000),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'bB8BunnYx12',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $creators[0]->id,
                'category_id' => $createdCats['filmes']->id,
                'title' => 'Sintel - Curta Metragem de Animação 3D (Completo)',
                'slug' => 'sintel-curta',
                'description' => "Sintel is an independently produced short film, initiated by the Blender Foundation as a means to further improve and validate the free/open source 3D creation suite Blender.",
                'video_url' => 'https://media.w3.org/2010/05/sintel/trailer_hd.mp4',
                'thumbnail_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/c8/Sintel_poster.jpg/800px-Sintel_poster.jpg',
                'tags' => 'animation,blender,sintel,dragon',
                'views_count' => rand(50000, 200000),
                'likes_count' => rand(2000, 5000),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'sINt3LdRaG0',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $creators[0]->id,
                'category_id' => $createdCats['filmes']->id,
                'title' => 'Tears of Steel - Sci-Fi Short Film Oficial',
                'slug' => 'tears-of-steel',
                'description' => "Tears of Steel was realized with crowd-funding by users of the open source 3D creation tool Blender.",
                'video_url' => 'https://media.w3.org/2010/05/bunny/trailer.mp4',
                'thumbnail_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/e/e0/Tears_of_Steel_poster.jpg/800px-Tears_of_Steel_poster.jpg',
                'tags' => 'scifi,movie,blender,vfx',
                'views_count' => rand(100000, 400000),
                'likes_count' => rand(8000, 15000),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'tEArsOfST3L',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $creators[1]->id,
                'category_id' => $createdCats['tecnologia']->id,
                'title' => 'Elephants Dream (2006) - O Primeiro Filme Aberto da História',
                'slug' => 'elephants-dream',
                'description' => "The first open movie, made entirely with open source graphics software.",
                'video_url' => 'https://interactive-examples.mdn.mozilla.net/media/cc0-videos/flower.mp4',
                'thumbnail_url' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/2/22/Elephants_Dream_poster.jpg/800px-Elephants_Dream_poster.jpg',
                'tags' => 'animation,classic,blender',
                'views_count' => rand(5000, 50000),
                'likes_count' => rand(500, 1000),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'eL3PhAnT506',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $creators[1]->id,
                'category_id' => $createdCats['vlogs']->id,
                'title' => 'ForBiggerBlazes - Teste de Fogo e Efeitos Práticos na Câmera',
                'slug' => 'for-bigger-blazes',
                'description' => "Vídeo de teste gratuito da biblioteca Google.",
                'video_url' => 'https://media.w3.org/2010/05/video/movie_300.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1542204165-65bf26472b9b?q=80&w=1280&auto=format&fit=crop',
                'tags' => 'vlog,fire,test',
                'views_count' => rand(1000, 10000),
                'likes_count' => rand(100, 500),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'fORbIgg3rBL',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $admin->id,
                'category_id' => $createdCats['vlogs']->id,
                'title' => 'Bem Vindo ao Lumina - O Futuro dos Vídeos é Agora!',
                'slug' => 'bem-vindo-ao-lumina',
                'description' => "Testando a plataforma Lumina com um vídeo oficial de introdução.",
                'video_url' => 'https://media.w3.org/2010/05/bunny/movie.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1517404215738-15263e9f9178?q=80&w=1280&auto=format&fit=crop',
                'tags' => 'lumina,platform,welcome',
                'views_count' => rand(5000, 15000),
                'likes_count' => rand(300, 900),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'LUMin4WelC0',
                'created_at' => now()->subDays(rand(1, 5))
            ],
            [
                'user_id' => $creators[2]->id,
                'category_id' => $createdCats['games']->id,
                'title' => 'ForBiggerJoyrides - Gameplay Test Drive Insano [1080p]',
                'slug' => 'gameplay-test-drive',
                'description' => "Vídeo focado na diversão e entretenimento.",
                'video_url' => 'https://www.w3schools.com/html/mov_bbb.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1538481199705-c710c4e965fc?q=80&w=1280&auto=format&fit=crop',
                'tags' => 'gameplay,joyride,fun',
                'views_count' => rand(20000, 80000),
                'likes_count' => rand(2000, 4000),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'g4M3pL4Yt3s',
                'created_at' => now()->subDays(rand(1, 30))
            ],
            [
                'user_id' => $admin->id,
                'category_id' => $createdCats['educacao']->id,
                'title' => 'Como utilizar a plataforma Lumina (Tutorial Passo a Passo)',
                'slug' => 'tutorial-lumina-plataforma',
                'description' => "Neste tutorial rápido mostramos como navegar e aproveitar o Lumina ao máximo.",
                'video_url' => 'https://www.w3schools.com/html/movie.mp4',
                'thumbnail_url' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=1280&auto=format&fit=crop',
                'tags' => 'tutorial,educacao,plataforma',
                'views_count' => rand(1000, 5000),
                'likes_count' => rand(50, 200),
                'visibility' => 'public',
                'status' => 'active',
                'youtube_id' => 'Tut0ri4LLuM',
                'created_at' => now()->subDays(1)
            ]
        ];

        foreach ($realVideos as $videoData) {
            $thumbUrl = $videoData['thumbnail_url'];
            unset($videoData['thumbnail_url']);
            $videoData['thumbnail'] = $thumbUrl; 
            
            Video::create($videoData);
        }

        // 5. Simular Inscrições Entre Contas (Subscriptions)
        // Admin se inscreve no Blender e Tech Explorer
        DB::table('subscriptions')->insert([
            ['subscriber_id' => $admin->id, 'channel_id' => $creators[0]->id, 'created_at' => now(), 'updated_at' => now()],
            ['subscriber_id' => $admin->id, 'channel_id' => $creators[1]->id, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Criadores se inscrevem no Admin para simular que o Admin tem inscritos
        DB::table('subscriptions')->insert([
            ['subscriber_id' => $creators[0]->id, 'channel_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
            ['subscriber_id' => $creators[1]->id, 'channel_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
            ['subscriber_id' => $creators[2]->id, 'channel_id' => $admin->id, 'created_at' => now(), 'updated_at' => now()],
        ]);
        
        // 6. Simular "Mais de 100 Contas Invisíveis" se inscrevendo nos canais
        // Como criar 100 contas pesa, vou apenas injetar o contador de inscritos para dar efeito de grandeza 
        // e inserir algumas notificações falsas pro Admin ver que tá "movimentado".
        
        DB::table('notifications')->insert([
            ['user_id' => $admin->id, 'from_user_id' => $creators[0]->id, 'type' => 'new_subscriber', 'message' => 'Blender Foundation se inscreveu no seu canal!', 'url' => '/canal/@blender', 'is_read' => false, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $admin->id, 'from_user_id' => $creators[1]->id, 'type' => 'like', 'message' => 'Tech Explorer curtiu seu vídeo.', 'url' => '/watch?v=LUMin4WelC0', 'is_read' => false, 'created_at' => now(), 'updated_at' => now()],
            ['user_id' => $admin->id, 'from_user_id' => $creators[2]->id, 'type' => 'new_comment', 'message' => 'GamePlays BR comentou no seu vídeo.', 'url' => '/watch?v=Tut0ri4LLuM', 'is_read' => false, 'created_at' => now(), 'updated_at' => now()]
        ]);

    }
}
