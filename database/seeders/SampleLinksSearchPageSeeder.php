<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
class SampleLinksSearchPageSeeder extends Seeder
{
    //region Links Data
    private array $linksToInsert = [
        //region romania
        //region liga 1
        [
            'competition_id' => 1,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/romania/liga-1/17088/?bt=matchresult',
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
        //endregion
        //region anglia
        //region premier league
        [
            'competition_id' => 3,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/anglia/premier-league/1/?bt=matchresult',
        ],
        [
            'competition_id' => 3,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/anglia/premier-league/toate?ct=m',
        ],
        [
            'competition_id' => 3,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-1?tab=matches&filter=all',
        ],
        //endregion
        //region league one
        [
            'competition_id' => 4,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/anglia/league-one/527/?bt=matchresult',
        ],
        [
            'competition_id' => 4,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/anglia/league-one/toate?ct=',
        ],
        [
            'competition_id' => 4,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-1/anglia-3?tab=matches&filter=all',
        ],
        //endregion
        //region league two
        [
            'competition_id' => 5,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/anglia/league-two/4/?bt=matchresult',
        ],
        [
            'competition_id' => 5,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/anglia/league-two/toate?ct=m',
        ],
        [
            'competition_id' => 5,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-1/anglia-4?tab=matches&filter=all',
        ],
        //endregion
        //endregion
        //region franta
        //region ligue 1
        [
            'competition_id' => 6,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/franta/ligue-1/215/?bt=matchresult',
        ],
        [
            'competition_id' => 6,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/franta/ligue-1/toate?ct=m',
        ],
        [
            'competition_id' => 6,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/franta-1/franta-ligue-1?tab=matches&filter=all',
        ],
        //endregion
        //region ligue 2
        [
            'competition_id' => 7,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/franta/ligue-2/10467/',
        ],
        [
            'competition_id' => 7,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/franta/ligue-2/toate?ct=m',
        ],
        [
            'competition_id' => 7,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/franta-1/franta-2?tab=matches&filter=all',
        ],
        //endregion
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
