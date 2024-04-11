<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;

class SeleniumController extends Controller
{
    private const BETANO_LIG1 = "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/";
    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adresa Selenium Server

    public function fetchData()
    {
        // Creare opțiuni pentru Firefox
        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->addArguments(['--headless']); // Rulează Firefox în mod headless (fără interfață grafică)
        
        // Creare capacitații dorite pentru Firefox
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());

        try {
            $betanoMatches = $this->scrapeDemoBetanoMatches($capabilities);
            return view('selenium', compact("betanoMatches"));
        } catch (\Exception $e) {
            dd($e);
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
