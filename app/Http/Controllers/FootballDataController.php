<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Laravel\Dusk\Browser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
//my class
use App\Services\DateConversionService;
use App\Helpers\StringHelper;

class FootballDataController extends Controller
{

    private array $dataUrlSearch = [
        // 'euro2024' => [
        //     "betano_url" => "https://ro.betano.com/sport/fotbal/competitii/euro/189663/?bt=matchresult",
        //     "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/international/euro-2024-gra/toate?ti=16144&cpi=1&ct=m",
        //     "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/euro-2024-meciuri"
        // ],
        'ro_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/romania/romania-superliga",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/romania-1"
        ],
        "germania_bundesliga" =>[
            "betano_url" => "https://ro.betano.com/sport/fotbal/germania/bundesliga/216/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/germania/germania-bundesliga/toate?ti=245",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/germania-bundesliga"
        ],
        "anglia_premier_league" =>[
            "betano_url" => "https://ro.betano.com/sport/fotbal/anglia/premier-league/1/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/anglia/anglia-premier-league/toate?ti=106",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/anglia-premier-league"
        ],
        'italia_seria_a' =>[
            'betano_url' => "https://ro.betano.com/sport/fotbal/competitii/italia/87/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/italia/italia-serie-a/toate?ti=104",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/italia-serie-a"
        ],
        'franta_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/franta/ligue-1/215/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/franta/franta-ligue-1/toate?ti=100",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/franta-ligue-1"
        ],
        'turcia_liga1' => [
            "betano_url" => "https://ro.betano.com/sport/fotbal/competitii/turcia/11384/",
            "suberbet_url" => "https://superbet.ro/pariuri-sportive/fotbal/turcia/turcia-super-lig/toate?ti=323",
            "casapariurilor_url" => "https://www.casapariurilor.ro/pariuri-online/fotbal/turcia-1"
        ],
    ];

    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adress Selenium Server

    public function fetchData()
    {

        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->addArguments(['--headless']); 
        
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
                $urlSuperbet = $urlData['suberbet_url'];
                $urlCasapariurilor = $urlData['casapariurilor_url'];

                $betanoMatches = $this->scrapeBetanoWithScriptMethod($urlBetano ,$capabilities);
                //betano is the main site where I searched matches ( if don't exist don't search to others sites)
                if(empty($betanoMatches)){
                    Log::info("No matches were found for betano in the league ($keyLigName)");
                    continue;
                }
                $superbetMatches = $this->scrapeSuperbetWithClassNameMethod($urlSuperbet ,$capabilities);
                $casapariurilorMatches = $this->scrapeCasaPariurilorWithClassNameMethod($urlCasapariurilor ,$capabilities);

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
    private function scrapeBetanoWithScriptMethod($urlSearchMatches, $capabilities)
    {
        $dataReturn = [];

        // Creare un nou driver WebDriver pentru Selenium
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);

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
            
        } finally {
            $driver->quit();
        }

        return $dataReturn;
    }
    //endregion
    
    //region superbet
    private function scrapeSuperbetWithClassNameMethod($urlSearchMatches ,$capabilities,$waitTimeout = 5, $waitPresenceTimeout = 5){
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);
        $superbetMatches = [];
        try {
            $driver->get($urlSearchMatches);
            try{
            //wait until the page is ready
                $driver->wait($waitTimeout)->until(
                    function ($driver) {
                        return $driver->executeScript('return document.readyState') === 'complete';
                    }
                );
                
                
                $driver->wait($waitPresenceTimeout)->until(
                    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('single-event'))
                );
            } catch (\Exception $e) {
                //Facebook\WebDriver\Exception\TimeoutException $e
                // Return default data if TimeoutException is encountered                
                return $superbetMatches;
            }
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
        } finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }

    }
    //endregion
    
    //region casa_pariurilor
    //I am used scrol to get all matches 
    private function scrapeCasaPariurilorWithClassNameMethod($urlSearchMatches, $capabilities,$waitTimeout = 10, $waitPresenceTimeout = 5){
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);
        $casaPariurilorMatches = [];

        try {
            $driver->get($urlSearchMatches);
            $driver->wait($waitTimeout)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );
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
            echo "A apărut o eroare: " . $e->getMessage();
            $driver->quit();
            exit;
        }finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }

    }
    //endregion
    
    //region test methods
    private function scrapeDemoBetanoMatches($capabilities, $waitTimeout = 10, $waitPresenceTimeout = 5)
    {
        // Creare driver WebDriver pentru Firefox
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);

        try {
            // Navigare către o pagină web
            $driver->get(self::BETANO_LIG1);

            // Așteaptă până când pagina este complet încărcată
            $driver->wait($waitTimeout)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );

            // Așteaptă până când elementele sunt prezente și vizibile pe pagină
            $driver->wait($waitPresenceTimeout)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vue-recycle-scroller__item-view'))
            );

            $matches = $driver->findElements(WebDriverBy::className('vue-recycle-scroller__item-view'));
            $betanoMatches = [];
            foreach ($matches as $match) {
                $teamsElements = $match->findElements(WebDriverBy::className('tw-text-n-13-steel'));
                $teamName1Element = $teamsElements[0];
                $teamName1 = $teamName1Element->getText();

                $teamName2Element = $teamsElements[1];
                $teamName2 = $teamName2Element->getText();

                $key = "$teamName1-$teamName2";

                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => ''];

                $detailsBetElements = $match->findElements(WebDriverBy::className('tw-text-tertiary'));
                $detailsBet1 = $detailsBetElements[0]->getText();
                $detailsBetx = $detailsBetElements[1]->getText();
                $detailsBet2 = $detailsBetElements[2]->getText();

                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;

                $betDetails['1'] = $detailsBet1;
                $betDetails['x'] = $detailsBetx;
                $betDetails['2'] = $detailsBet2;

                $betanoMatches[$key] = $betDetails;
            }

            return $betanoMatches;
        } finally {
            // Închide driverul WebDriver în blocul finally pentru a ne asigura că se închide întotdeauna
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }
    //endregion
}
