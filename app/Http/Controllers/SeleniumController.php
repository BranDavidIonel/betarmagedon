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

class SeleniumController extends Controller
{
    //fotbal liga 1
    private const BETANO_LIG1 = "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/";
    private const SUPERBET_LIG1 = "https://superbet.ro/pariuri-sportive/fotbal/romania/romania-superliga-playoff/toate";

    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adresa Selenium Server

    public function fetchData()
    {
        // Creare opțiuni pentru Firefox
        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->addArguments(['--headless']); // Rulează Firefox în mod headless (fără interfață grafică)
        
        // Creare capacitații dorite pentru Firefox
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());

        $betanoMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '']];
        $superbetMatches = [['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => '']];
        try {
            //$betanoMatches = $this->scrapeDemoBetanoMatches($capabilities);
            $betanoMatches = $this->scrapeBetanoWithScriptMethod($capabilities);
            $superbetMatches = $this->scrapeSuperbetWithXPathMethod($capabilities);
            //dd($superbetMatches);
            return view('selenium', compact("betanoMatches","superbetMatches"));
        } catch (\Exception $e) {
            dd($e);
        }
    }
    private function scrapeBetanoWithScriptMethod($capabilities)
    {
        $dataReturn = null;

        // Creare un nou driver WebDriver pentru Selenium
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);

        try {
            // Accesează pagina Betano
            $driver->get(self::BETANO_LIG1);

            // Define XPath expression to target the desired script element
            // $xpathExpression = "//script[contains(text(), 'window[\"initial_state\"]')]";

            // // Așteaptă până când este prezent un element care să corespundă XPath-ului dat
            // $wait = new WebDriverWait($driver, 10);
            // $scriptElement = $wait->until(
            //     WebDriverExpectedCondition::presenceOfElementLocated(WebDriverBy::xpath($xpathExpression))
            // );

            $pageSource = $driver->getPageSource();
            $driver->quit();

            // Caută conținutul dorit în sursa paginii
            $pattern = '/window\["initial_state"\]\s*=\s*(.*?)\s*}<\/script>/s';
            preg_match($pattern, $pageSource, $matches);
            //dd($matches);
            $scriptContent = [];
            if (isset($matches[1])) {
                $initialStateJson = $matches[1]."}";
                $scriptContent = json_decode($initialStateJson, true);
            }

            $driver->quit();
            $matchesDataFromScripts = isset($scriptContent['data']['blocks']) ? $scriptContent['data']['blocks'][0]['events'] : [];
            foreach($matchesDataFromScripts as $matchScript){
                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => ''];

                $teamName1 = $matchScript['participants'][0]['name'];
                $teamName2 = $matchScript['participants'][1]['name'];

                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => ''];

                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;

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
            // Închide driver-ul la final
            $driver->quit();
        }

        return $dataReturn;
    }
    private function scrapeSuperbetWithXPathMethod($capabilities,$waitTimeout = 10, $waitPresenceTimeout = 5){
        $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);

        try {
            // Navigare către o pagină web
            $driver->get(self::SUPERBET_LIG1);

            // Așteaptă până când pagina este complet încărcată
            $driver->wait($waitTimeout)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );

            // Așteaptă până când elementele sunt prezente și vizibile pe pagină
            $driver->wait($waitPresenceTimeout)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('single-event'))
            );

            $matches = $driver->findElements(WebDriverBy::className('single-event'));
            $betanoMatches = [];
            foreach ($matches as $match) {
                $teamsElements = $match->findElements(WebDriverBy::className('event-competitor__name'));
                $teamName1Element = $teamsElements[0];
                $teamName1 = $teamName1Element->getText();

                $teamName2Element = $teamsElements[1];
                $teamName2 = $teamName2Element->getText();

                $key = "$teamName1-$teamName2";

                $betDetails = ['team1Name' => '', 'team2Name' => '', '1' => '', 'x' => '', '2' => ''];
                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;

                $detailsBetElements = $match->findElements(WebDriverBy::className('e2e-odd-current-value'));
                if(!empty($detailsBetElements)){
                    $detailsBet1 = $detailsBetElements[0]->getText();
                    $detailsBetx = $detailsBetElements[1]->getText();
                    $detailsBet2 = $detailsBetElements[2]->getText();

                    $betDetails['1'] = $detailsBet1;
                    $betDetails['x'] = $detailsBetx;
                    $betDetails['2'] = $detailsBet2;
                }
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
}
