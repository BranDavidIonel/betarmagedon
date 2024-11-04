<?php

namespace App\Services;

use App\Models\LinksSearchPage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class SaveMatchService
{
    /**
     * Function to insert a new match record into the database using DB::table.
     *
     * @param string|$linkLeague
     * @param array|$matchData
     * @return bool True if insertion was successful, false otherwise
     */
    public function insertScrapedMatch( string $linkLeague, array $matchData,  string $type): bool
    {
        $existingLink = LinksSearchPage::where('link_league', $linkLeague)->first();
        if(empty($existingLink)) {
            Log::error("function insertScrapedMatch -> I don't find a link for league $linkLeague ");
            return false;
        }
        if(empty($matchData)) {
            return false;
        }

        $team1Name = $matchData['team1Name'];
        $team2Name = $matchData['team2Name'];
        $bet1 = $matchData['1'];
        $betX = $matchData['x'];
        $bet2 = $matchData['2'];
        $odds = [ "1" => $bet1, "x" => $betX, "2" => $bet2];
        $startTime = $matchData['startTime'];

        try {
            // Attempt to convert $startTime from "d-m-Y H:i" to "Y-m-d H:i:s"
            $formattedStartTime = $startTime ? (new \DateTime($startTime))->format('Y-m-d H:i:s') : null;
        } catch (Exception $e) {
            Log::error("Failed to parse start time: '{$startTime}'. Expected format: 'd-m-Y H:i'.", [
                'exception' => $e->getMessage(),
                'input_start_time' => $startTime
            ]);
            return false;
        }
        try {
            // Insert data directly into the database using DB::table
            return DB::table('scraped_matches')->insert([
                'link_search_page_id' => $existingLink->id,
                'team1_name' => $team1Name,
                'team2_name' => $team2Name,
                'odds' => json_encode($odds),
                'start_time' => $formattedStartTime,
                'type' => $type,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        } catch (Exception $e) {
            Log::error("Failed to insert match into scraped_matches table.", [
                'exception' => $e->getMessage(),
                'data' => [
                    'team1_name' => $team1Name,
                    'team2_name' => $team2Name,
                    'odds' => $odds,
                    'start_time' => $startTime,
                    'type' => $type,
                ]
            ]);
            return false;
        }
    }
}
