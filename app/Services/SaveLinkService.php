<?php
//app/Services/DateConversionService.php

namespace App\Services;

use Carbon\Carbon;
use App\Models\SitesSearch;
use App\Models\LinksSearchPage;
use App\Models\Competition;
class SaveLinkService
{
    public function __construct()
    {
    }

    public function insertLinkIfNotExists($idSite, $typeGame, $linkLeague, $competitionName, $countryName)
    {
        $competition = $this->findOrCreateCompetition($competitionName, $countryName);
        $existingLink = LinksSearchPage::where('link_league', $linkLeague)->first();

        if (!$existingLink) {
            return LinksSearchPage::create([
                'id_site' => $idSite,
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
        $competition = Competition::where('name', 'like','%'.$competitionName.'%')
                        ->where('country_name', $countryName)
                        ->first();

        if (!$competition) {
            $competition = Competition::create([
                'name' => $competitionName,
                'country_name' => $countryName,
                'alias' => json_encode([$competitionName])
            ]);
        }

        return $competition;
    }
}
