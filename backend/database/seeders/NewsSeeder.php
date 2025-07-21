<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class NewsSeeder extends Seeder
{
    public function run()
    {
        $categories = DB::table('news_categories')->pluck('id')->all();
        $images = [
            'https://images.unsplash.com/photo-1506744038136-46273834b3fb?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1517649763962-0c623066013b?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1465101046530-73398c7f28ca?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&w=400&q=80',
            'https://images.unsplash.com/photo-1519125323398-675f0ddb6308?auto=format&fit=crop&w=400&q=80',
        ];
        for ($i = 1; $i <= 5; $i++) {
            DB::table('news')->insert([
                'title' => 'Nieuwsitem ' . $i,
                'summary' => 'Dit is een korte samenvatting van nieuwsitem ' . $i . '.',
                'content' => 'Volledige inhoud van nieuwsitem ' . $i . '. Lorem ipsum dolor sit amet, consectetur adipiscing elit.',
                'image_url' => $images[$i-1],
                'published_at' => Carbon::now()->subDays(6 - $i),
                'news_category_id' => $categories ? $categories[array_rand($categories)] : null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
} 