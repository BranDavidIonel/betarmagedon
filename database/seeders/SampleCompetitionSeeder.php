<?php

namespace Database\Seeders;

use App\Models\Competition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleCompetitionSeeder extends Seeder
{

    private $competitions = [
        //region romania
        [
            'id' => 1,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'liga 1',
            'alias' => ['liga 1', 'superliga', 'romania 1']
        ],
        [
            'id' => 2,
            'country_id' => 140,
            'country_name' => 'romania',
            'name' => 'liga 2',
            'alias' => ['liga 2', 'romania 2', 'romania liga 2 casa pariurilor']
        ],
        //endregion
        //region anglia
        [
            'id' => 3,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'premier league',
            'alias' => ['premier league', 'anglia 1']
        ],
        [
            'id' => 4,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league one',
            'alias' => ['league one', 'anglia 3']
        ],
        [
            'id' => 5,
            'country_id' => 6,
            'country_name' => 'anglia',
            'name' => 'league two',
            'alias' => ['league two', 'anglia 4']
        ],

        //endregion
        //region franta
        [
            'id' => 6,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'ligue 1',
            'alias' => ['ligue 1', 'franta 1']
        ],
        [
            'id' => 7,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'ligue 2',
            'alias' => ['ligue 2', 'franta 2']
        ],
        //endregion
        //region germania
        [
            'id' => 8,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga',
            'alias' => ['bundesliga', 'germania bundesliga', 'germania-bundesliga-1']
        ],
        [
            'id' => 9,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'germania cupa',
            'alias' => ['germania cupa', 'germania cupa']
        ],
        [
            'id' => 10,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => 'bundesliga 2',
            'alias' => ['bundesliga 2', 'germania 2', 'germania-2-1']
        ],
        [
            'id' => 11,
            'country_id' => 68,
            'country_name' => 'germania',
            'name' => '3 liga',
            'alias' => ['3 liga', 'germania 3', 'germania-3-1']
        ],

        //endregion

    ];
    public function run(): void
    {
        $competitions = $this->competitions;

        foreach ($competitions as $competition) {
            // Check if the competition already exists by name
            $exists = Competition::where('name', $competition['name'])->exists();

            // Only insert if it doesn't exist
            if (!$exists) {
                Competition::create([
                    'name' => $competition['name'],
                    'country_id' => $competition['country_id'],
                    'alias' => json_encode($competition['alias']) // encode alias as JSON
                ]);
            }
        }

    }
}
