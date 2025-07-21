<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class NewsCategorySeeder extends Seeder
{
    public function run()
    {
        DB::table('news_categories')->insert([
            [
                'name' => 'Clubnieuws',
                'slug' => 'clubnieuws',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Wedstrijden',
                'slug' => 'wedstrijden',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Bestuur',
                'slug' => 'bestuur',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
} 