<?php
//app/Services/DateConversionService.php

namespace App\Services;

use App\Models\Country;
use Carbon\Carbon;
use App\Models\SitesSearch;
use App\Models\LinksSearchPage;
use App\Models\Competition;
use Illuminate\Support\Facades\Log;
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
        $competition = Competition::where('name', '=', $competitionName)
            ->where('country_id', $countryId)
            ->first();
        //search in alias name
        if(empty($competition)){
            $competition = Competition::where('country_id', $countryId)
                ->whereJsonContains('alias', $competitionName)
                ->first();
        }
        //for casa pariurilor add alias ( romanania ('liga 1', '1') for name liga 1
        $competition = $this->checkAndAddNewAliasNameForCompetition($competitionName, $competition, $countryId);

        if (!$competition) {
            $competition = Competition::create([
                'name' => $competitionName,
                'country_id' => $countryId,
                'alias' => json_encode([$competitionName])
            ]);
        }

        return $competition;
    }

    public function checkAndAddNewAliasNameForCompetition($competitionName, $competition, $countryId)
    {
        $competitionName = trim($competitionName);
        if (is_numeric($competitionName) && empty($competition)) {
            $competition = Competition::where('name', 'like',  '%'.$competitionName)
                ->where('country_id', $countryId)
                ->first();
            if (!empty($competition)) {
                // Decode the existing aliases from JSON format
                $aliases = json_decode($competition->alias, true);
                // Check if the current alias ($competitionName) is not already in the list of aliases
                if (!in_array($competitionName, $aliases)) {
                    // Add the new alias to the array of aliases
                    $aliases[] = $competitionName;
                    // Encode the updated aliases back to JSON and save the changes
                    $competition->alias = json_encode($aliases);
                    $competition->save();
                    return $competition;
                }
            }
        }
        return $competition;
    }
}
