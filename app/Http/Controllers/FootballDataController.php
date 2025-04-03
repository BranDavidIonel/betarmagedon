<?php

namespace App\Http\Controllers;

use App\Models\Competition;
use App\Services\AccepCookiesButtonService;
use App\Services\SaveMatchService;
use App\Services\ScrapeSitesService;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Collection;
use Laravel\Dusk\Browser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//my class
use App\Services\DateConversionService;
use App\Services\ConfigWebDriverService;
use App\Models\LinksSearchPage;
use Illuminate\Support\Facades\DB;

class FootballDataController extends Controller
{
    //region main data
    private ConfigWebDriverService $configWebDriverService;
    private SaveMatchService $saveMatchService;

    private ScrapeSitesService $scrapeSitesService;
    //demo data
    private array $dataUrlSearch = [
        'ro_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/romania/superliga-playoff/toate",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/romania-3/romania-1?tab=matches&filter=all"
        ],
        /*
        "germania_bundesliga" =>[
            "betano_url" => "https://ro.betano.com/sport/fotbal/germania/bundesliga/216/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/germania/germania-bundesliga/toate?ti=245",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/germania-bundesliga"
        ],
        "anglia_premier_league" =>[
            "betano_url" => "https://ro.betano.com/sport/fotbal/anglia/premier-league/1/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/anglia/anglia-premier-league/toate?ti=106",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-premier-league"
        ],
        'italia_seria_a' =>[
            'betano_url' => "https://ro.betano.com/sport/fotbal/competitii/italia/87/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/italia/italia-serie-a/toate?ti=104",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/italia-serie-a"
        ],
        'franta_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/franta/ligue-1/215/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/franta/franta-ligue-1/toate?ti=100",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/franta-ligue-1"
        ],
        'turcia_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/competitii/turcia/11384/",
            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/turcia/turcia-super-lig/toate?ti=323",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/turcia-1"
        ],
        */
    ];
    //private array $dataUrlSearch = [];
    public function __construct(ConfigWebDriverService $configWebDriverService, SaveMatchService $saveMatchService, ScrapeSitesService $scrapeSitesService)
    {
        $this->configWebDriverService = $configWebDriverService;
        $this->saveMatchService = $saveMatchService;
        $this->scrapeSitesService = $scrapeSitesService;
        //$this->dataUrlSearch = $this->getDataUrlSearchFromQuery();
    }
    private function getDataUrlSearchFromQuery():array
    {
        $results = LinksSearchPage::select(
            'lsp.competition_id',
            'com.name AS competition_name',
            'com.alias AS competition_alias',
            'countries.name AS country_name',
            DB::raw('GROUP_CONCAT(lsp.site_id ORDER BY
                            CASE
                                WHEN lsp.site_id = 1 THEN 1
                                WHEN lsp.site_id = 2 THEN 2
                                WHEN lsp.site_id = 3 THEN 3
                                ELSE 4 /* un fallback pentru site_id-uri necunoscute */
                            END ASC
                        ) AS site_ids'),
            DB::raw('GROUP_CONCAT(lsp.link_league ORDER BY
                            CASE
                                WHEN lsp.site_id = 1 THEN 1
                                WHEN lsp.site_id = 2 THEN 2
                                WHEN lsp.site_id = 3 THEN 3
                                ELSE 4 /* un fallback pentru link-uri necunoscute */
                            END ASC
                        ) AS links')
            )
            ->from('links_search_page AS lsp')
            ->join('competitions AS com', 'com.id', '=', 'lsp.competition_id')
            ->join('countries', 'countries.id', '=', 'com.country_id')
            ->groupBy('lsp.competition_id', 'com.name', 'com.alias', 'countries.name')
            ->havingRaw('COUNT(DISTINCT lsp.site_id) > 2')
            ->get();

        $formattedLinks = [];
        foreach ($results as $result) {
            // Get the sites from `links` using explode
            $linksArray = explode(',', $result->links);
            $siteIdsArray = explode(',', $result->site_ids);
            // Initialize an array to store the links for each competition
            $competitionKey =$result->country_name. " -> ". $result->competition_name;
            // Check if there is already an entry for the competition
            if (!isset($formattedLinks[$competitionKey])) {
                $formattedLinks[$competitionKey] = [];
            }
            // Add the links directly, as they are already in the desired order
            foreach ($siteIdsArray as $index => $siteId) {
                switch ($siteId) {
                    case '1':
                        $formattedLinks[$competitionKey]['betano_url'] = $linksArray[$index];
                        break;
                    case '2':
                        $formattedLinks[$competitionKey]['superbet_url'] = $linksArray[$index];
                        break;
                    case '3':
                        $formattedLinks[$competitionKey]['casapariurilor_url'] = $linksArray[$index];
                        break;
                }
            }
        }

        return $formattedLinks;
    }

    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adress Selenium Server

    public function fetchData()
    {
        $firefoxOptions = new FirefoxOptions();
        $argumentsBrowser = [
            '--disable-gpu', // Evită problemele cu GPU
            '--no-sandbox',  // Necesitat pentru medii de container
            '--disable-dev-shm-usage', // Evită problemele cu memoria partajată
            '--window-size=1920x1080', // Setează dimensiunea fereastrei pentru vizualizare mai bună
            '--remote-debugging-port=5900' // Deschide un port pentru debugging remote
        ];
        //$argumentsBrowser = ['--headless'];
        $firefoxOptions->addArguments($argumentsBrowser);

        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());

        $returnAllMathcesData = [
            'league_name' =>
                ['betano_matches' => [],
                    'suberbet_matches' => [],
                    'casapariurilor_matches' => []
                ],
            'searchRezultMatches' => []
        ];
        $betanoMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        $superbetMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        $casapariurilorMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        try {
            $matchesUrls = $this->dataUrlSearch;
            foreach($matchesUrls as $keyLigName => $urlData){
                $randomNumberSleep = random_int(1, 7);
                sleep($randomNumberSleep);
                Log::info("begin search for: $keyLigName");
                $urlBetano = $urlData['betano_url'];
                $urlSuperbet = $urlData['superbet_url'];
                $urlCasapariurilor = $urlData['casapariurilor_url'];

                //$betanoMatches = $this->scrapeBetanoWithScriptMethod($urlBetano);
                $betanoMatches = $this->scrapeSitesService->scrapeBetanoWithScriptMethod($urlBetano);
                //betano is the main site where I searched matches ( if don't exist don't search to others sites)
                if(empty($betanoMatches)){
                    Log::info("No matches were found for betano in the league ($keyLigName)");
                    continue;
                }
                //$superbetMatches = $this->scrapeSuperbetWithClassNameMethod($urlSuperbet);
                $superbetMatches = $this->scrapeSitesService->scrapeSuperbetWithClassNameMethod($urlSuperbet);
                //$casapariurilorMatches = $this->scrapeCasaPariurilorWithClassNameMethod($urlCasapariurilor);
                $casapariurilorMatches = $this->scrapeSitesService->scrapeCasaPariurilorWithClassNameMethod($urlCasapariurilor);

                $returnAllMathcesData[$keyLigName] = [
                    'betano_matches' => $betanoMatches,
                    'suberbet_matches' => $superbetMatches,
                    'casapariurilor_matches' => $casapariurilorMatches
                ];


                $searchRezultMatches = [];
                foreach($betanoMatches as $betanoMatch){

                    if(!$this->validateMatch($betanoMatch)){
                        continue;//next match search
                    }
                    $findMatchSuperbet = $this->searchMatch($betanoMatch, $superbetMatches);
                    if(!$this->validateMatch($findMatchSuperbet)){
                        continue;//next match search
                    }
                    $findMatchCasapariurilor = $this->searchMatch($betanoMatch, $casapariurilorMatches);
                    if(!$this->validateMatch($findMatchCasapariurilor)){
                        continue;//next match search
                    }

                    $this->saveMatchService->insertScrapedMatch($urlBetano, $betanoMatch, 'betano_matches' );
                    $this->saveMatchService->insertScrapedMatch($urlSuperbet, $findMatchSuperbet, 'suberbet_matches' );
                    $this->saveMatchService->insertScrapedMatch($urlCasapariurilor, $findMatchCasapariurilor, 'casapariurilor_matches' );

                    $searchProfit = $this->getProfitMatchData($betanoMatch, $findMatchSuperbet, $findMatchCasapariurilor);
                    if(!empty($searchProfit)){
                        $searchRezultMatches[]= ['matchesData' => ['betano' => $betanoMatch , 'subertbet' => $findMatchSuperbet, 'casapariurilor' => $findMatchCasapariurilor],
                                                'resultData' => $searchProfit];
                    }
                }

                $returnAllMathcesData['searchRezultMatches'] = $searchRezultMatches;

                $searhHasProfit = $this->hasProfitData($searchRezultMatches);

                if(empty($searhHasProfit)){
                    Log::info("I didn't find anything");
                }else{
                    Log::alert("Bingo I found some sure match:",$searhHasProfit);
                }

                Log::info('Rezult matches details:', $searchRezultMatches);
                Log::info("end search for:$keyLigName");
            }
            //return $returnAllMathcesData;

            return view('scraped-data', compact("returnAllMathcesData"));
        } catch (\Exception $e) {
            dd($e);
        }
    }
    public function searchMatchesDataFromDB()
    {
        $scrapedMatches = DB::table("scraped_matches AS sm")
                        ->join("links_search_page AS lsp", "lsp.id", "=", "sm.link_search_page_id")
                        ->selectRaw("sm.id AS idScrapedMatches, lsp.site_id, lsp.competition_id, lsp.link_league as linkLeague,
                                                sm.link_search_page_id, sm.team1_name as team1Name, sm.team2_name as team2Name,
                                                sm.odds, sm.start_time as startTime, sm.updated_at as lastScrapedTime")
                        ->orderBy("start_time", "desc")->get();
        $scrapedMatches = $scrapedMatches->map(function($match) {
            $match->odds = json_decode($match->odds, true);
            return $match;
        });
        $comepetitionsIds = $scrapedMatches->pluck("competition_id")->toArray();

        $comepetitions = DB::table("competitions")->whereIn("id", $comepetitionsIds)
                        ->selectRaw("id, name")->get();
        $returnAllMathcesData = [];
        foreach ($comepetitions as $competition){
            $nameLeague = $competition->name;
            $betanoMatches = $scrapedMatches->where("competition_id", $competition->id)
                                            ->where("site_id", 1)
                                            ->toArray();
            $superbetMatches = $scrapedMatches->where("competition_id", $competition->id)
                                            ->where("site_id", 2)
                                            ->toArray();
            $casapariurilorMatches = $scrapedMatches->where("competition_id", $competition->id)
                ->where("site_id", 3)
                ->toArray();
            $returnAllMathcesData[$nameLeague] = [
                'betano_matches' => $betanoMatches,
                'suberbet_matches' => $superbetMatches,
                'casapariurilor_matches' => $casapariurilorMatches
            ];
            $searchRezultMatches = [];
            foreach($betanoMatches as $betanoMatch){
//                if (is_object($betanoMatch)) {
//                    $betanoMatch = json_decode(json_encode($betanoMatch), true);
//                }
                $betanoMatch = json_decode(json_encode($betanoMatch), true);

                if(!$this->validateMatch($betanoMatch)){
                    continue;//next match search
                }
                $superbetMatches = json_decode(json_encode($superbetMatches), true);
                $findMatchSuperbet = $this->searchMatch($betanoMatch, $superbetMatches);
                if(!$this->validateMatch($findMatchSuperbet)){
                    continue;//next match search
                }
                $casapariurilorMatches = json_decode(json_encode($casapariurilorMatches), true);
                $findMatchCasapariurilor = $this->searchMatch($betanoMatch, $casapariurilorMatches);
                if(!$this->validateMatch($findMatchCasapariurilor)){
                    continue;//next match search
                }

                $searchProfit = $this->getProfitMatchData($betanoMatch, $findMatchSuperbet, $findMatchCasapariurilor);
                if(!empty($searchProfit)){
                    $searchRezultMatches[]= ['matchesData' => ['betano' => $betanoMatch , 'subertbet' => $findMatchSuperbet, 'casapariurilor' => $findMatchCasapariurilor],
                        'profitData' => $searchProfit];
                }
            }
            $searhHasProfit = $this->hasProfitData($searchRezultMatches);
            $returnAllMathcesData[$nameLeague]['searhHasProfit'] = $searhHasProfit;
            $returnAllMathcesData[$nameLeague]['detailsProfit'] = $searchRezultMatches;
        }
        //dd($returnAllMathcesData);
        //return view('football-type-cards', compact("returnAllMathcesData"));
        return view('football', compact("returnAllMathcesData"));
    }
    //endregion

    //region search is profit match
    private function hasProfitData($profitData){
        if (isset($profitData) && is_array($profitData) && count($profitData) > 0) {
            foreach ($profitData as $data) {
                if (isset($data['resultData']['isProfit']) && $data['resultData']['isProfit'] === true) {
                    $reversOdds = isset($data['resultData']['reversOdds']) ? $data['resultData']['reversOdds'] : 0;
                    $matchInfo = ['reversOdds' => $reversOdds , 'details' => $data['matchesData']];
                    return $matchInfo;
                }
            }
        }
        return false;
    }
    private function searchMatch($matchFind,$matchesSearch){
        $dateTimeFind = $matchFind['startTime'];
        //I have some bug about time in casapariurilor site( is search without time from date)
        $dateTimeFindArray = explode(" ", $dateTimeFind);
        $onlyDateFind = isset($dateTimeFindArray[0]) ? $dateTimeFindArray[0] : null;

        $team1NameFind = $matchFind['team1Name'];
        $team2NameFind = $matchFind['team2Name'];


        foreach($matchesSearch as $matchSearch){
            $dateTimeSearch = $matchSearch['startTime'];
            $dateTimeSearchArray = explode(" ", $dateTimeSearch);
            $onlyDateSearch = isset($dateTimeSearchArray[0]) ? $dateTimeSearchArray[0] : null;

            $team1NameSearch = $matchSearch['team1Name'];
            $team2NameSearch = $matchSearch['team2Name'];

            $percentFindTeam1 = calculateSimilarityStringsPercentage($team1NameFind, $team1NameSearch);
            $percentFindTeam2 = calculateSimilarityStringsPercentage($team2NameFind, $team2NameSearch);

            if($onlyDateSearch == $onlyDateFind && ($percentFindTeam1 > 60 && $percentFindTeam2 > 60)){
                return $matchSearch;
            }
        }

        return false;
    }


    private function getProfitMatchData($matchA,$matchB,$matchC){
        if(!$this->validateMatch($matchA)){
            return false;
        }

        $Ateam1name = $matchA['team1Name'];
        $Ateam2name = $matchA['team2Name'];
        $Aodds = $matchA['odds'];
        $Abet1 = $Aodds['1'];
        $Abetx = $Aodds['x'];
        $Abet2 = $Aodds['2'];
        //$AstartTime = $matchA['startTime'];

        if(!$this->validateMatch($matchB)){
            return false;
        }
        $Bteam1name = $matchB['team1Name'];
        $Bteam2name = $matchB['team2Name'];
        $Bodds = $matchB['odds'];
        $Bbet1 = $Bodds['1'];
        $Bbetx = $Bodds['x'];
        $Bbet2 = $Bodds['2'];
        //$BstartTime = $matchB['startTime'];


        if(!$this->validateMatch($matchC)){
            return false;
        }

        $Cteam1name = $matchC['team1Name'];
        $Cteam2name = $matchC['team2Name'];
        $Codds = $matchA['odds'];
        $Cbet1 = $Codds['1'];
        $Cbetx = $Codds['x'];
        $Cbet2 = $Codds['2'];
        //$CstartTime = $matchC['startTime'];

        $maxBet1 = max($Abet1,$Bbet1,$Cbet1);//best odds if the first team wins
        $maxBetx = max($Abetx,$Bbetx,$Cbetx);//the best odds if it is a draw
        $maxBet2 = max($Abet2,$Bbet2,$Cbet2);//best odds if the second team wins

        //if the reverse of the odds is less than 1 , then it is profit
        $reverseOdds = 1/floatval($maxBet1) + 1/floatval($maxBetx) + 1/floatval($maxBet2);
        $returnData = ['reversOdds' => 0, 'isProfit' => false, 'maxBets'=>[ '1' => $maxBet1, 'x' => $maxBetx, '2' => $maxBet2]];
        $returnData['reversOdds'] = $reverseOdds;
        if($reverseOdds < 1){
            $returnData['isProfit'] = true;
        }

        return $returnData;

    }

    private function validateMatch($match){
        if(empty($match)){
            return false;
        }
        $team1name = $match['team1Name'];
        $team2name = $match['team2Name'];
        $odds = $match['odds'];
        $bet1 = $odds['1'];
        $betx = $odds['x'];
        $bet2 = $odds['2'];
        $startTime = $match['startTime'];
        $isLive = false;
        if(empty($team1name) || empty($team2name) || empty($bet1) || empty($betx) || empty($bet2) || empty($startTime)){
            Log::error("In validateMatch method validation failed details -> ", $match);
            return false;
        }
        return true;
    }
    //endregion

}
