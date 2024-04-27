<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Laravel\Dusk\Browser;
use DOMDocument;
use Carbon\Carbon;
//my class
use App\Services\DateConversionService;

class FootballDataController extends Controller
{
    //region fotbal liga 1
    private const BETANO_LIG1 = "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/";
    private const SUPERBET_LIG1 = "https://superbet.ro/pariuri-sportive/fotbal/romania/romania-superliga-playoff/toate";
    private const CASAPARIURILOR_LIG1 = "https://www.casapariurilor.ro/pariuri-online/fotbal/romania-1";
    //endregion

    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adresa Selenium Server

    public function fetchData()
    {

        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->addArguments(['--headless']); // Rulează Firefox în mod headless (fără interfață grafică)
        
        // Creare capacitații dorite pentru Firefox
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());

        $betanoMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        $superbetMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        $casapariurilorMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => '']];
        try {
            //$betanoMatches = $this->scrapeDemoBetanoMatches($capabilities);
            $betanoMatches = $this->scrapeBetanoWithScriptMethod($capabilities);
            $superbetMatches = $this->scrapeSuperbetWithClassNameMethod($capabilities);
            $casapariurilorMatches = $this->scrapeCasaPariurilorWithClassNameMethod($capabilities);
            //dd($casapariurilorMatches);
            return view('football', compact("betanoMatches","superbetMatches","casapariurilorMatches"));
        } catch (\Exception $e) {
            dd($e);
        }
    }
    //region betano
    private function scrapeBetanoWithScriptMethod($capabilities)
    {
        $dataReturn = null;

        // Creare un nou driver WebDriver pentru Selenium
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);

        try {
            // Accesează pagina Betano
            $driver->get(self::BETANO_LIG1);

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
            foreach($matchesDataFromScripts as $matchScript){
                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '', 'startTime' => '', 'isLive' => ''];

                $teamName1 = $matchScript['participants'][0]['name'];
                $teamName2 = $matchScript['participants'][1]['name'];
                $timestamp = $matchScript['startTime']/1000;

                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;

                $dateStartMatch = Carbon::createFromTimestamp($timestamp);
                $betDetails['startTime'] = $dateStartMatch->addHours(3)->format('d-m-Y H:i');
                $betDetails['isLive'] = isset($matchScript['liveNow']) ? true : false;

                $detailsBet1 = $matchScript['markets'][0]['selections'][0]['price'];
                $detailsBetx = $matchScript['markets'][0]['selections'][1]['price'];
                $detailsBet2 = $matchScript['markets'][0]['selections'][2]['price'];

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
    private function scrapeSuperbetWithClassNameMethod($capabilities,$waitTimeout = 10, $waitPresenceTimeout = 5){
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);
        $superbetMatches = [];
        try {
            $driver->get(self::SUPERBET_LIG1);

            //wait until the page is ready
            $driver->wait($waitTimeout)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );

            $driver->wait($waitPresenceTimeout)->until(
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
    private function scrapeCasaPariurilorWithClassNameMethod($capabilities,$waitTimeout = 10, $waitPresenceTimeout = 5){
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);
        $casaPariurilorMatches = [];

        try {
            $driver->get(self::CASAPARIURILOR_LIG1);
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
