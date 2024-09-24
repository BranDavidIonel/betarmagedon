<?php
//app/Services/DateConversionService.php

namespace App\Services;

use App\Models\Country;
use Carbon\Carbon;
use App\Models\SitesSearch;
use App\Models\LinksSearchPage;
use App\Models\Competition;
class SaveLinkService
{
    public function __construct()
    {
    }

    public function insertLinkIfNotExists($siteId, $typeGame, $linkLeague, $competitionName, $countryName)
    {
        $competition = $this->findOrCreateCompetition($competitionName, $countryName);
        $existingLink = LinksSearchPage::where('link_league', $linkLeague)->first();

        if (!$existingLink) {
            return LinksSearchPage::create([
                'site_id' => $siteId,
                'type_game' => $typeGame,
                'link_league' => $linkLeague,
                'with_data' => false,
                'competition_id' => $competition->id,
            ]);
        }

        return "Link already exists!";
    }

    private function findOrCreateCompetition($competitionName, $countryName)
    {
        $countryId = null;
        $findCountry = Country::where('name', $countryName)->first();
        if($findCountry){
            $countryId = $findCountry->id;
        }

        $competition = Competition::where('name', 'like','%'.$competitionName.'%')
                        ->where('country_id', $countryId)
                        ->first();

        if (!$competition) {
            $competition = Competition::create([
                'name' => $competitionName,
                'country_id' => $countryId,
                'alias' => json_encode([$competitionName])
            ]);
        }

        return $competition;
    }
}
