<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SampleLinksSearchPageSeeder extends Seeder
{
    //region Links Data
    private array $linksToInsert = [
        //region liga 1
        [
            'competition_id' => 1,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/romania/liga-1/17088/',
        ],
        [
            'competition_id' => 1,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/romania/superliga/toate?ct=m',
        ],
        [
            'competition_id' => 1,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/romania-3/romania-1?tab=matches&filter=all',
        ],
        //endregion
        //region franta 1
        [
            'competition_id' => 3,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/franta/ligue-1/215/',
        ],
        [
            'competition_id' => 3,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/franta/ligue-1/toate?ct=m',
        ],
        [
            'competition_id' => 3,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/franta-1/franta-ligue-1?tab=matches&filter=all',
        ],

        //endregion
        // Add more entries here for other competitions or sites
    ];
    //endregion
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->linksToInsert as $link) {
            // Insert the record if it doesn't already exist
            DB::table('links_search_page')->updateOrInsert(
                [
                    'competition_id' => $link['competition_id'],
                    'site_id' => $link['site_id'],
                    'link_league' => $link['link_league'],
                    'type_game' => $link['type_game'],
                ],
                [
                    'with_data' => false,
                    'scraped' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}
