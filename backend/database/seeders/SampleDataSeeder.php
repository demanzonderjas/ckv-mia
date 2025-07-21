<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Models\MenuLink;
use App\Models\Page;
use App\Models\ContentBlock;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create Teams
        $teamA = Team::create([
            'name' => 'CKV MIA 1',
            'description' => 'Eerste team van CKV MIA',
            'category' => 'wedstrijdkorfbal',
        ]);
        $teamB = Team::create([
            'name' => 'CKV MIA 2',
            'description' => 'Tweede team van CKV MIA',
            'category' => 'wedstrijdkorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 3',
            'description' => 'Derde team van CKV MIA',
            'category' => 'wedstrijdkorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 4',
            'description' => 'Vierde team van CKV MIA',
            'category' => 'wedstrijdkorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 5',
            'description' => 'Vijfde team van CKV MIA',
            'category' => 'breedtekorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 6',
            'description' => 'Zesde team van CKV MIA',
            'category' => 'breedtekorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 7',
            'description' => 'Zevende team van CKV MIA',
            'category' => 'breedtekorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 8',
            'description' => 'Achtste team van CKV MIA',
            'category' => 'breedtekorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA 9',
            'description' => 'Negende team van CKV MIA',
            'category' => 'breedtekorfbal',
        ]);
        Team::create([
            'name' => 'CKV MIA MW1',
            'description' => 'Midweek team 1',
            'category' => 'midweek',
        ]);
        Team::create([
            'name' => 'CKV MIA MW2',
            'description' => 'Midweek team 2',
            'category' => 'midweek',
        ]);
        Team::create([
            'name' => 'CKV MIA MW3',
            'description' => 'Midweek team 3',
            'category' => 'midweek',
        ]);

        // Create 16 junior teams (J1–J16)
        for ($i = 1; $i <= 16; $i++) {
            Team::create([
                'name' => 'CKV MIA J' . $i,
                'description' => 'Jeugdteam ' . $i,
                'category' => 'jeugd',
                'type' => 'junior',
            ]);
        }

        // Create Events
        Event::create([
            'name' => 'Training Session',
            'description' => 'Weekly team training at the club.',
            'date' => now()->addDays(2)->format('Y-m-d 19:00:00'),
            'location' => 'CKV MIA Clubhouse',
            'team_id' => $teamA->id,
        ]);
        Event::create([
            'name' => 'Home Match vs Rivals',
            'description' => 'Exciting home match against our rivals.',
            'date' => now()->addDays(7)->format('Y-m-d 14:00:00'),
            'location' => 'CKV MIA Field',
            'team_id' => $teamA->id,
        ]);
        Event::create([
            'name' => 'Club BBQ',
            'description' => 'End-of-month BBQ for all members and families.',
            'date' => now()->addDays(20)->format('Y-m-d 17:00:00'),
            'location' => 'CKV MIA Clubhouse',
            'team_id' => $teamB->id,
        ]);
        Event::create([
            'name' => 'September Tournament',
            'description' => 'Big tournament in September.',
            'date' => '2025-09-13 10:00:00',
            'location' => 'CKV MIA Field',
            'team_id' => $teamA->id,
        ]);
        Event::create([
            'name' => 'Season Kickoff',
            'description' => 'Start of the new season.',
            'date' => '2025-09-20 15:00:00',
            'location' => 'CKV MIA Clubhouse',
            'team_id' => $teamB->id,
        ]);
        $team = Team::first();
        Event::create([
            'name' => 'Event with Team',
            'description' => 'This event is linked to a team.',
            'date' => now()->addDays(5),
            'location' => 'Sporthal',
            'team_id' => $team ? $team->id : null,
        ]);
        Event::create([
            'name' => 'Event for All Teams',
            'description' => 'This event is for all teams.',
            'date' => now()->addDays(10),
            'location' => 'Clubhuis',
            'team_id' => null,
        ]);

        // Create Members
        $teams = \App\Models\Team::all();
        $firstNames = ['Anna', 'Bram', 'Clara', 'Daan', 'Eva', 'Finn', 'Gwen', 'Hugo', 'Iris', 'Jens', 'Kim', 'Lars', 'Mila', 'Noah', 'Olaf', 'Pien', 'Quinn', 'Rosa', 'Sven', 'Tess'];
        $lastNames = ['Jansen', 'de Vries', 'Pieters', 'Bakker', 'Visser', 'Smit', 'Meijer', 'Mulder', 'Bos', 'de Boer', 'Vos', 'de Groot', 'van Dijk', 'van Leeuwen', 'de Bruin', 'van der Meer', 'van Dam', 'van Vliet', 'de Lange', 'van den Berg'];
        foreach ($teams as $team) {
            $numPlayers = $team->type === 'junior' ? rand(7, 10) : rand(8, 10);
            $used = [];
            for ($i = 0; $i < $numPlayers; $i++) {
                do {
                    $first = $firstNames[array_rand($firstNames)];
                    $last = $lastNames[array_rand($lastNames)];
                    $email = strtolower($first . '.' . $last . $team->id . $i . '@example.com');
                } while (in_array($email, $used));
                $used[] = $email;
                $gender = rand(0, 1) ? 'male' : 'female';
                \App\Models\Member::create([
                    'first_name' => $first,
                    'last_name' => $last,
                    'email' => $email,
                    'phone' => '06' . rand(10000000, 99999999),
                    'team_id' => $team->id,
                    'gender' => $gender,
                ]);
            }
        }

        

        // Seed example pages and content blocks
        $about = Page::create([
            'title' => 'Over CKV MIA',
            'slug' => 'about',
            'description' => 'Meer over onze club.'
        ]);
        $contact = Page::create([
            'title' => 'Contact',
            'slug' => 'contact',
            'description' => 'Contactinformatie van CKV MIA.'
        ]);
        $clubInfo = Page::create([
            'title' => 'Club Info',
            'slug' => 'club-info',
            'description' => 'Algemene informatie over CKV MIA.'
        ]);
        ContentBlock::create([
            'page_id' => $about->id,
            'type' => 'text',
            'content' => json_encode(['text' => 'CKV MIA is een gezellige korfbalvereniging in Amersfoort.']),
            'order' => 1
        ]);
        ContentBlock::create([
            'page_id' => $contact->id,
            'type' => 'text',
            'content' => json_encode(['text' => 'Neem contact op via info@ckvmia.nl of bezoek ons op Schothorsterlaan 5, Amersfoort.']),
            'order' => 1
        ]);
        ContentBlock::create([
            'page_id' => $clubInfo->id,
            'type' => 'text',
            'content' => json_encode(['text' => 'CKV MIA biedt sportieve activiteiten voor jong en oud.']),
            'order' => 1
        ]);
        $newsPage = Page::create([
            'title' => 'Nieuws',
            'slug' => 'news',
            'description' => 'Het laatste nieuws van CKV MIA.'
        ]);
        ContentBlock::create([
            'page_id' => $newsPage->id,
            'type' => 'text',
            'content' => json_encode(['text' => 'Welkom op de nieuwspagina van CKV MIA! Hier vind je het laatste nieuws, updates en aankondigingen.']),
            'order' => 1
        ]);
    }
}
