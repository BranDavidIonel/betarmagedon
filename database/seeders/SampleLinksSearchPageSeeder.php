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
        //region germania
        //region bundesliga
        [
            'competition_id' => 8,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/germania/bundesliga/216/?bt=matchresult',
        ],
        [
            'competition_id' => 8,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/germania/bundesliga/toate?ct=m',
        ],
        [
            'competition_id' => 8,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/germania-2/germania-bundesliga-1?tab=matches&filter=all',
        ],
        //endregion
        //region germania cupa (dfb pokal)
        [
            'competition_id' => 9,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/germania/dfb-pokal/10486/?bt=matchresult',
        ],
        [
            'competition_id' => 9,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/germania/dfb-pokal/toate?ct=m',
        ],
        [
            'competition_id' => 9,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/germania-2/germania-cupa?tab=matches&filter=all',
        ],
        //endregion
        //region bundesliga 2
        [
            'competition_id' => 10,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/germania/2-bundesliga/217/?bt=matchresult',
        ],
        [
            'competition_id' => 10,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/germania/bundesliga-2/toate?ct=m',
        ],
        [
            'competition_id' => 10,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/germania-2/germania-2-1?tab=matches&filter=all',
        ],
        //endregion
        //region 3 liga
        [
            'competition_id' => 11,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/germania/3-liga/17313/?bt=matchresult',
        ],
        [
            'competition_id' => 11,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/germania/3-liga/toate?ct=m',
        ],
        [
            'competition_id' => 11,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/germania-2/germania-3-1?tab=matches&filter=all',
        ],
        //endregion
        //endregion
        //region italia
        //region serie a
        [
            'competition_id' => 12,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/italia/serie-a/1635/',
        ],
        [
            'competition_id' => 12,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/italia/serie-a/toate?ct=m',
        ],
        [
            'competition_id' => 12,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/italia-3/italia-serie-a-1?tab=matches&filter=all',
        ],
        //endregion
        //region coppa italia
        [
            'competition_id' => 13,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/italia/coppa-italia/10815/',
        ],
        [
            'competition_id' => 13,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/italia/coppa-italia/toate?ct=m',
        ],
        [
            'competition_id' => 13,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/italia-3/italia-cupa-1?tab=matches&filter=all',
        ],
        //endregion
        //region serie-b
        [
            'competition_id' => 14,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/italia/serie-b/10210/',
        ],
        [
            'competition_id' => 14,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/italia/serie-b/toate?ct=m',
        ],
        [
            'competition_id' => 14,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/italia-3/italia-2?tab=matches&filter=all',
        ],
        //endregion
        //endregion
        //region spania
        //region laliga
        [
            'competition_id' => 15,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/spania/laliga/5/',
        ],
        [
            'competition_id' => 15,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/spania/laliga/toate?ct=m',
        ],
        [
            'competition_id' => 15,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/spania-6/spania-la-liga?tab=matches&filter=all',
        ],
        //endregion
        //region cupa regelui
        [
            'competition_id' => 16,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/spania/cupa-regelui/10067/',
        ],
        [
            'competition_id' => 16,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/spania/copa-del-rey/toate?ct=or',
        ],
        [
            'competition_id' => 16,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/spania-6/spania-cupa-1?tab=matches&filter=all',
        ],
        //endregion
        //region laliga 2
        [
            'competition_id' => 17,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/spania/segunda-division/10000/',
        ],
        [
            'competition_id' => 17,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/spania/laliga-2/toate?ct=m',
        ],
        [
            'competition_id' => 17,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/spania-6/spania-2?tab=matches&filter=all',
        ],
        //endregion
        //endregion
        //region austria
        //region bundesliga
        [
            'competition_id' => 18,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/austria/bundesliga/16823/',
        ],
        [
            'competition_id' => 18,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/austria/bundesliga/toate?ct=m',
        ],
        [
            'competition_id' => 18,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/austria-2?tab=matches&filter=all',
        ],
        //endregion
        //endregion
        //region belgia
        //region pro league
        [
            'competition_id' => 19,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/belgia/1a-pro-league/16849/?bt=matchresult',
        ],
        [
            'competition_id' => 19,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/belgia/pro-league/toate?ct=m',
        ],
        [
            'competition_id' => 19,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/belgia/belgia-1?filter=all&tab=matches',
        ],
        //endregion
        //endregion
        //region danemarca
        //region
        [
            'competition_id' => 20,
            'site_id' => 1,
            'type_game' => 'football',
            'link_league' => 'https://ro.betano.com/sport/fotbal/danemarca/superligaen/16955/?bt=matchresult',
        ],
        [
            'competition_id' => 20,
            'site_id' => 2,
            'type_game' => 'football',
            'link_league' => 'https://superbet.ro/pariuri-sportive/fotbal/danemarca/superliga/toate?ct=m',
        ],
        [
            'competition_id' => 20,
            'site_id' => 3,
            'type_game' => 'football',
            'link_league' => 'https://www.casapariurilor.ro/pariuri-online/fotbal/danemarca-3/danemarca-1-1?filter=all&tab=matches',
        ],
        //endregion
        //endregion
        // Add more entries here for other sites
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
