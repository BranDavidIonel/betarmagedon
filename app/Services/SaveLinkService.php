<?php
//app/Services/DateConversionService.php

namespace App\Services;

use App\Models\Country;
use Carbon\Carbon;
use App\Models\SitesSearch;
use App\Models\LinksSearchPage;
use App\Models\Competition;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
class SaveLinkService
{
    public function __construct()
    {
    }

    public function insertLinkIfNotExists($siteId, $typeGame, $linkLeague, $competitionName, $countryName)
    {
        $linkLeague = trim($linkLeague);
        $competitionName = trim($competitionName);
        $countryName = trim($countryName);

        $competition = $this->findOrCreateCompetition($competitionName, $countryName);
        if(empty($competition)){
            return "Don't exist competition!";
        }

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
    public function createScrapedCompetition($siteId, $competitionName, $countryName)
    {
        $countryId = null;
        $findCountry = false;
        if(!empty($countryName)) {
            $findCountry = Country::where('name', 'like', '%' . $countryName)->first();
        }
        if($findCountry){
            $countryId = $findCountry->id;
        }
        $checkExistDataAboutSite = DB::table('scraped_competitions')
                                    ->where('site_id', $siteId)
                                    ->where('name', $competitionName)
                                    ->where('country_id', $countryId)
                                    ->where('country_name', $countryName)
                                    ->first();
        if(empty($checkExistDataAboutSite)) {
            DB::table('scraped_competitions')->insert([
                'site_id' => $siteId,
                'name' => $competitionName,
                'country_id' => $countryId,
                'country_name' => $countryName,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }
    //nu vreau sa o mai fac doar sa caut in ea
    private function findOrCreateCompetition($competitionName, $countryName)
    {
        $countryId = null;
        $findCountry = false;
        if(!empty($countryName)) {
            $findCountry = Country::where('name', 'like', '%' . $countryName)->first();
        }
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

        //$competition = $this->checkAndAddNewAliasNameForCompetition($competitionName, $competition, $countryId);

//        if (!$competition) {
//            $competition = Competition::create([
//                'name' => $competitionName,
//                'country_id' => $countryId,
//                'alias' => json_encode([$competitionName])
//            ]);
//        }

        return $competition;
    }
    //trebuie sa scot atatea verificari
//    private function checkAndAddNewAliasNameForCompetition($competitionName, $competition, $countryId)
//    {
//        $competitionName = trim($competitionName);
//        if (is_numeric($competitionName) && empty($competition)) {
//            $competition = Competition::where('name', 'like',  '%'.$competitionName)
//                ->where('country_id', $countryId)
//                ->first();
//            if (!empty($competition)) {
//                // Decode the existing aliases from JSON format
//                $aliases = json_decode($competition->alias, true);
//                // Check if the current alias ($competitionName) is not already in the list of aliases
//                if (!in_array($competitionName, $aliases)) {
//                    // Add the new alias to the array of aliases
//                    $aliases[] = $competitionName;
//                    // Encode the updated aliases back to JSON and save the changes
//                    $competition->alias = json_encode($aliases);
//                    $competition->save();
//                    return $competition;
//                }
//            }
//        }
//        return $competition;
//    }
}
