<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Tecnologia', 'icon' => '💻'],
            ['name' => 'Educação', 'icon' => '📚'],
            ['name' => 'Entretenimento', 'icon' => '🎉'],
            ['name' => 'Música', 'icon' => '🎵'],
            ['name' => 'Games', 'icon' => '🎮'],
            ['name' => 'Esportes', 'icon' => '⚽'],
            ['name' => 'Notícias', 'icon' => '📰'],
            ['name' => 'Culinária', 'icon' => '🍳'],
            ['name' => 'Viagens', 'icon' => '✈️'],
            ['name' => 'Humor', 'icon' => '😂'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'icon' => $category['icon'],
            ]);
        }
    }
}
