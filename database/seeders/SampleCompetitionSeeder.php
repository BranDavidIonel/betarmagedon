<?php

namespace Database\Seeders;

use App\Models\Competition;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SampleCompetitionSeeder extends Seeder
{

    private $competitions = [
        //region romania data
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
        //region franta data
        [
            'id' => 3,
            'country_id' => 64,
            'country_name' => 'franta',
            'name' => 'ligue 1',
            'alias' => ['ligue 1', 'franta 1', 'romania 1']
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
