<?php

namespace App\Http\Controllers;

use App\Services\AccepCookiesButtonService;
use App\Services\SaveMatchService;
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
    //demo data
//    private array $dataUrlSearch = [
//        'ro_liga1' => [
//            "betano_url" => "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/romania/romania-superliga",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/romania-1"
//        ],
//        "germania_bundesliga" =>[
//            "betano_url" => "https://ro.betano.com/sport/fotbal/germania/bundesliga/216/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/germania/germania-bundesliga/toate?ti=245",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/germania-bundesliga"
//        ],
//        "anglia_premier_league" =>[
//            "betano_url" => "https://ro.betano.com/sport/fotbal/anglia/premier-league/1/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/anglia/anglia-premier-league/toate?ti=106",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-premier-league"
//        ],
//        'italia_seria_a' =>[
//            'betano_url' => "https://ro.betano.com/sport/fotbal/competitii/italia/87/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/italia/italia-serie-a/toate?ti=104",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/italia-serie-a"
//        ],
//        'franta_liga1' => [
//            "betano_url" => "https://ro.betano.com/sport/fotbal/franta/ligue-1/215/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/franta/franta-ligue-1/toate?ti=100",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/franta-ligue-1"
//        ],
//        'turcia_liga1' => [
//            "betano_url" => "https://ro.betano.com/sport/fotbal/competitii/turcia/11384/",
//            "superbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/turcia/turcia-super-lig/toate?ti=323",
//            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/turcia-1"
//        ],
//    ];
    private array $dataUrlSearch = [];
    public function __construct(ConfigWebDriverService $configWebDriverService, SaveMatchService $saveMatchService)
    {
        $this->configWebDriverService = $configWebDriverService;
        $this->saveMatchService = $saveMatchService;
        $this->dataUrlSearch = $this->getDataUrlSearchFromQuery();
        //dd($this->dataUrlSearch);
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

        $returnAllMathcesData = ['league_name' => ['betano_matches' => [], 'suberbet_matches' => [], 'casapariurilor_matches' => []]];
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

                $betanoMatches = $this->scrapeBetanoWithScriptMethod($urlBetano);
                //betano is the main site where I searched matches ( if don't exist don't search to others sites)
                if(empty($betanoMatches)){
                    Log::info("No matches were found for betano in the league ($keyLigName)");
                    continue;
                }
                $superbetMatches = $this->scrapeSuperbetWithClassNameMethod($urlSuperbet);
                $casapariurilorMatches = $this->scrapeCasaPariurilorWithClassNameMethod($urlCasapariurilor);

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
                $searhHasProfit = $this->hasProfitData($searchRezultMatches);

                if(empty($searhHasProfit)){
                    Log::info("I didn't find anything");
                }else{
                    Log::alert("Bingo I found some sure match:",$searhHasProfit);
                }

                Log::info('Rezult matches details:', $searchRezultMatches);
                Log::info("end search for:$keyLigName");
            }

            return view('football', compact("returnAllMathcesData"));
        } catch (\Exception $e) {
            dd($e);
        }
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
        $dateFind = $matchFind['startTime'];
        $team1NameFind = $matchFind['team1Name'];
        $team2NameFind = $matchFind['team2Name'];


        foreach($matchesSearch as $matchSearch){
            $dateSearch = $matchSearch['startTime'];
            $team1NameSearch = $matchSearch['team1Name'];
            $team2NameSearch = $matchSearch['team2Name'];

            $percentFindTeam1 = calculateSimilarityStringsPercentage($team1NameFind, $team1NameSearch);
            $percentFindTeam2 = calculateSimilarityStringsPercentage($team2NameFind, $team2NameSearch);


            if($dateSearch == $dateFind && ($percentFindTeam1 > 60 && $percentFindTeam2 > 60)){
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
        $Abet1 = $matchA['1'];
        $Abetx = $matchA['x'];
        $Abet2 = $matchA['2'];
        $AstartTime = $matchA['startTime'];
        $AisLive = $matchA['isLive'];

        if(!$this->validateMatch($matchB)){
            return false;
        }
        $Bteam1name = $matchB['team1Name'];
        $Bteam2name = $matchB['team2Name'];
        $Bbet1 = $matchB['1'];
        $Bbetx = $matchB['x'];
        $Bbet2 = $matchB['2'];
        $BstartTime = $matchB['startTime'];
        $BisLive = $matchB['isLive'];


        if(!$this->validateMatch($matchC)){
            return false;
        }

        $Cteam1name = $matchC['team1Name'];
        $Cteam2name = $matchC['team2Name'];
        $Cbet1 = $matchC['1'];
        $Cbetx = $matchC['x'];
        $Cbet2 = $matchC['2'];
        $CstartTime = $matchC['startTime'];
        $CisLive = $matchC['isLive'];

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
        $bet1 = $match['1'];
        $betx = $match['x'];
        $bet2 = $match['2'];
        $startTime = $match['startTime'];
        $isLive = $match['isLive'];
        if(empty($team1name) || empty($team2name) || empty($bet1) || empty($betx) || empty($bet2) || empty($startTime)){
            return false;
        }
        //now I don't want live
        if(!empty($isLive)){
            return false;
        }

        return true;
    }
    //endregion

    //region betano
    private function scrapeBetanoWithScriptMethod($urlSearchMatches)
    {
        $dataReturn = [];
        // Creare un nou driver WebDriver pentru Selenium
        $driver = $this->configWebDriverService->initializeWebDriver();
        try {
            // Accesează pagina Betano
            $driver->get($urlSearchMatches);

            $pageSource = $driver->getPageSource();
            $driver->quit();

            //caut scriptul care contine toate datele inceput si sfarsit
            $pattern = '/window\["initial_state"\]\s*=\s*(.*?)\s*}<\/script>/s';
            preg_match($pattern, $pageSource, $matches);
            $scriptContent = [];
            if (isset($matches[1])) {
                $initialStateJson = $matches[1]."}";
                $scriptContent = json_decode($initialStateJson, true);
            }

            $driver->quit();

            $matchesDataFromScripts = isset($scriptContent['data']['blocks']) ? $scriptContent['data']['blocks'][0]['events'] : [];
            //dd($matchesDataFromScripts);
            foreach($matchesDataFromScripts as $matchScript){
                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => ''];

                //don't exist the match
                if(!isset($matchScript['participants'][0]['name']) || !isset($matchScript['participants'][1]['name'])){
                    continue;
                }
                $teamName1 = $matchScript['participants'][0]['name'];
                $teamName2 = $matchScript['participants'][1]['name'];
                $timestamp = $matchScript['startTime']/1000;

                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;

                $dateStartMatch = Carbon::createFromTimestamp($timestamp);
                $betDetails['startTime'] = $dateStartMatch->addHours(3)->format('d-m-Y H:i');
                $betDetails['isLive'] = isset($matchScript['liveNow']) ? true : false;
                $detailsBetFromScript = $matchScript['markets'][0]['selections'];
                if(empty($detailsBetFromScript)){
                    continue;//I need details about 1 | x | 2 teams
                }

                $detailsBet1 = $detailsBetFromScript[0]['price'];
                $detailsBetx = $detailsBetFromScript[1]['price'];
                $detailsBet2 = $detailsBetFromScript[2]['price'];

                $betDetails['1'] = $detailsBet1;
                $betDetails['x'] = $detailsBetx;
                $betDetails['2'] = $detailsBet2;

                $key = "$teamName1-$teamName2";
                $dataReturn[$key] = $betDetails;
            }

        }catch (\Exception $e) {
            Log::error('eroare scrapeBetanoWithScriptMethod',$e->getTrace());
            echo "A apărut o eroare scrapeBetanoWithScriptMethod: " . $e->getMessage(). "linia: ".$e->getLine();
            $driver->quit();
            exit;
        }finally {
            $driver->quit();
        }
        return $dataReturn;
    }
    //endregion

    //region superbet
    private function scrapeSuperbetWithClassNameMethod($urlSearchMatches){
        $driver = $this->configWebDriverService->initializeWebDriver();
        $superbetMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            //don't work without this ( check is page ready like above)
            $driver->wait(6)->until(
                    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('single-event'))
            );
            //events-by-date , is card with multiples matches group on date
            $cardDatesElements = $driver->findElements(WebDriverBy::className('events-by-date'));
            foreach($cardDatesElements as $cardElement){
                $dateMatchElement = $cardElement->findElement(WebDriverBy::className('events-date'));
                $dateFormatRo = $dateMatchElement->getText();
                $matches = $cardElement->findElements(WebDriverBy::className('single-event'));

                foreach ($matches as $match) {
                    try{
                        $teamsElements = $match->findElements(WebDriverBy::className('event-competitor__name'));
                        //capitalize -> hour and minutes get string
                        $hourMinutesElement = $match->findElement(WebDriverBy::className('capitalize'));
                    }catch(\Exception $e){
                        //for live matches i have this error
//                        Log::error("eroare superbet");
//                        Log::error($e->getMessage());
                        continue; //next match
                    }
                    $teamName1Element = $teamsElements[0];
                    $teamName1 = $teamName1Element->getText();

                    $teamName2Element = $teamsElements[1];
                    $teamName2 = $teamName2Element->getText();

                    $key = "$teamName1-$teamName2";

                    $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => ''];
                    $betDetails['team1Name'] = $teamName1;
                    $betDetails['team2Name'] = $teamName2;

                    $stringHourMinutes = $hourMinutesElement->getText();
                    $stringHourMinutes = substr($stringHourMinutes, strpos($stringHourMinutes, ',')+1);
                    list($hour, $minutes) = explode(':', trim($stringHourMinutes));

                    $convertedDate = DateConversionService::convertDateROtoCarbon($dateFormatRo);
                    $betDetails['startTime'] = $convertedDate->setTime(intval($hour), intval($minutes), 0)->addHours(3)->format('d-m-Y H:i');
                    $betDetails['isLive'] = false;

                    $detailsBetElements = $match->findElements(WebDriverBy::className('e2e-odd-current-value'));
                    if(!empty($detailsBetElements)){
                        $detailsBet1 = $detailsBetElements[0]->getText();
                        $detailsBetx = $detailsBetElements[1]->getText();
                        $detailsBet2 = $detailsBetElements[2]->getText();

                        $betDetails['1'] = $detailsBet1;
                        $betDetails['x'] = $detailsBetx;
                        $betDetails['2'] = $detailsBet2;
                    }
                    $superbetMatches[$key] = $betDetails;
                }
            }
            return $superbetMatches;
        }catch (\Exception $e) {
            Log::error('eroare scrapeSuperbetWithClassNameMethod',$e->getTrace());
            echo "A apărut o eroare scrapeSuperbetWithClassNameMethod:" . $e->getMessage(). "linia: ".$e->getLine();
            $driver->quit();
            dd($e);
            exit;
        } finally {
            $driver->quit();
        }
    }
    //endregion

    //region casa_pariurilor
    //I am used scrol to get all matches
    private function scrapeCasaPariurilorWithClassNameMethod($urlSearchMatches){
        $driver = $this->configWebDriverService->initializeWebDriver();
        $casaPariurilorMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            // Call the function to handle cookie consent
            AccepCookiesButtonService::acceptCookiesCasaPariurilor($driver);
            //get top of the page
            $driver->executeScript('window.scrollTo(0, 0);');
            $matches = $driver->findElements(WebDriverBy::className('tablesorter-hasChildRow'));

            foreach ($matches as $match) {
                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => ''];
                try{
                    $teamNamesElement = $match->findElement(WebDriverBy::className('market-name'));

                    if(!empty($teamNamesElement)){
                        $teamNames = $teamNamesElement->getText();
                        if(empty($teamNames)){
                            $scrollHeight = $driver->executeScript('return document.body.scrollHeight;');
                            $scrollDistance = $scrollHeight / 15; // Scrolează pe a 15 parte (putin) din înălțimea paginii
                            $driver->executeScript("window.scrollTo(0, {$scrollDistance});");
                            sleep(1);
                            $teamNamesElement = $match->findElement(WebDriverBy::className('market-name'));
                        }
                    }
                }catch(\Exception $e){
                    continue; //next match
                }

                if(!empty($teamNamesElement)){
                    $teamNames = $teamNamesElement->getText();
                    $arrayNames = explode('-', $teamNames);

                    $teamName1 = isset($arrayNames[0]) ? trim($arrayNames[0]) : "";

                    $teamName2 = isset($arrayNames[1]) ? trim($arrayNames[1]) : "";
                    $key = "$teamName1-$teamName2";
                    $betDetails['team1Name'] = $teamName1;
                    $betDetails['team2Name'] = $teamName2;
                }
                try {
                    $elementBet1 = $match->findElement(WebDriverBy::cssSelector('td:nth-child(2) > a > span'));
                    $elementBetx = $match->findElement(WebDriverBy::cssSelector('td:nth-child(3) > a > span'));
                    $elementBet2 = $match->findElement(WebDriverBy::cssSelector('td:nth-child(4) > a > span'));
                    $elementDateTime = $match->findElement(WebDriverBy::cssSelector('td.col-date[data-value]'));
                    $dateTimeInMS = $elementDateTime->getAttribute('data-value');//date time in micro seconds

                }catch(\Exception $e){
                    continue; //next match
                }

                if(!empty($elementBet1 && !empty($elementBetx) && !empty($elementBet2))){
                    $detailsBet1 = $elementBet1->getText();
                    $detailsBetx = $elementBetx->getText();
                    $detailsBet2 = $elementBet2->getText();

                    $betDetails['1'] = $detailsBet1;
                    $betDetails['x'] = $detailsBetx;
                    $betDetails['2'] = $detailsBet2;

                    $timestamp = intval($dateTimeInMS) / 1000;
                    $dateStartMatch = Carbon::createFromTimestamp($timestamp);
                    $betDetails['startTime'] = $dateStartMatch->addHours(3)->format('d-m-Y H:i');
                }
                $casaPariurilorMatches[$key] = $betDetails;
            }

            return $casaPariurilorMatches;
        }catch (\Exception $e) {
            Log::error('eroare scrapeCasaPariurilorWithClassNameMethod',$e->getTrace());
            echo "A apărut o eroare superbet functia cautare scrapeCasaPariurilorWithClassNameMethod:" . $e->getMessage().' linia:'.$e->getLine();
            $driver->quit();
            exit;
        }finally {
            $driver->quit();
        }
    }

    //endregion

}
