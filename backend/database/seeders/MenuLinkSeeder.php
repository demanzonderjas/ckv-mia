<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class MenuLinkSeeder extends Seeder
{
    public function run()
    {
        DB::table('menu_links')->insert([
            // header links
            [
                'title' => 'Teams',
                'url' => '/teams',
                'order' => 1,
                'active' => true,
                'category' => 'header',
            ],
            [
                'title' => 'News',
                'url' => '/news',
                'order' => 2,
                'active' => true,
                'category' => 'header',
            ],
            [
                'title' => 'Events',
                'url' => '/events',
                'order' => 3,
                'active' => true,
                'category' => 'header',
            ],
            [
                'title' => 'Competition',
                'url' => '/competitions',
                'order' => 4,
                'active' => true,
                'category' => 'header',
            ],
            [
                'title' => 'About',
                'url' => '/about',
                'order' => 5,
                'active' => true,
                'category' => 'header',
            ],
            // side (quick links)
            [
                'title' => 'Contact',
                'url' => '/contact',
                'order' => 1,
                'active' => true,
                'category' => 'side',
            ],
            [
                'title' => 'Speelschema',
                'url' => '/speelschema',
                'order' => 2,
                'active' => true,
                'category' => 'side',
            ],
            [
                'title' => 'Teams',
                'url' => '/teams',
                'order' => 3,
                'active' => true,
                'category' => 'side',
            ],
        ]);
    }
} 