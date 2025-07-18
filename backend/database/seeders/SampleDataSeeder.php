<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Team;
use App\Models\Event;
use App\Models\Member;
use App\Models\SideMenuLink;

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

        // Create Members
        $teams = \App\Models\Team::all();
        $firstNames = ['Anna', 'Bram', 'Clara', 'Daan', 'Eva', 'Finn', 'Gwen', 'Hugo', 'Iris', 'Jens', 'Kim', 'Lars', 'Mila', 'Noah', 'Olaf', 'Pien', 'Quinn', 'Rosa', 'Sven', 'Tess'];
        $lastNames = ['Jansen', 'de Vries', 'Pieters', 'Bakker', 'Visser', 'Smit', 'Meijer', 'Mulder', 'Bos', 'de Boer', 'Vos', 'de Groot', 'van Dijk', 'van Leeuwen', 'de Bruin', 'van der Meer', 'van Dam', 'van Vliet', 'de Lange', 'van den Berg'];
        foreach ($teams as $team) {
            $numPlayers = rand(8, 10);
            $used = [];
            for ($i = 0; $i < $numPlayers; $i++) {
                do {
                    $first = $firstNames[array_rand($firstNames)];
                    $last = $lastNames[array_rand($lastNames)];
                    $email = strtolower($first . '.' . $last . $team->id . $i . '@example.com');
                } while (in_array($email, $used));
                $used[] = $email;
                \App\Models\Member::create([
                    'first_name' => $first,
                    'last_name' => $last,
                    'email' => $email,
                    'phone' => '06' . rand(10000000, 99999999),
                    'team_id' => $team->id,
                ]);
            }
        }

        SideMenuLink::create([
            'title' => 'Playing Schedule',
            'url' => '/schedule',
            'order' => 1,
            'active' => true,
            'description' => 'View the full playing schedule.'
        ]);
        SideMenuLink::create([
            'title' => 'Club Info',
            'url' => '/about',
            'order' => 2,
            'active' => true,
            'description' => 'Learn more about CKV MIA.'
        ]);
        SideMenuLink::create([
            'title' => 'Contact',
            'url' => '/contact',
            'order' => 3,
            'active' => true,
            'description' => 'Contact details and form.'
        ]);
    }
}
