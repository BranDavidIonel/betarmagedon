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
    public function fetchData()
    {
        // Adresa Selenium Server
        $serverUrl = 'http://selenium:4444/wd/hub';

        // Creare opțiuni pentru Firefox
        $firefoxOptions = new FirefoxOptions();
        $firefoxOptions->addArguments(['--headless']); // Rulează Firefox în mod headless (fără interfață grafică)
        
        // Creare capacitații dorite pentru Firefox
        $capabilities = DesiredCapabilities::firefox();
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());

        try {
            // Creare driver WebDriver pentru Firefox
            $driver = RemoteWebDriver::create($serverUrl, $capabilities);

            // Navigare către o pagină web
            $driver->get('https://ro.betano.com/sport/fotbal/romania/liga-1/17088/');


            // Așteaptă până când pagina este complet încărcată
            $driver->wait(10)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );

            // Așteaptă până când elementele sunt prezente și vizibile pe pagină
            $driver->wait(5)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vue-recycle-scroller__item-view'))
            );

            // $wait = new WebDriverWait($driver, 5);
            // $wait->until(
            //     WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vue-recycle-scroller__item-view'))
            // );

            $matches = $driver->findElements(WebDriverBy::className('vue-recycle-scroller__item-view'));
            $betanoMatches = [];
            foreach ($matches as $match) {
                $teamsElements = $match->findElements(WebDriverBy::className('tw-text-n-13-steel'));
                //$teamName1Element = $match->findElement(WebDriverBy::xpath('.//a/div/div[1]/div[1]/span'));
                $teamName1Element = $teamsElements[0];
                $teamName1 = $teamName1Element->getText();
        
                $teamName2Element = $teamsElements[1];
                $teamName2 = $teamName2Element->getText();

                $key = "$teamName1-$teamName2";

                $betDetails = ['team1Name' => '','team2Name' => '', '1' => '' , 'x' => '' , '2' => ''];

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

            // Închide driverul WebDriver
            $driver->quit();
            //dd($betanoMatches);
            
            return view('selenium', compact("betanoMatches"));
        } catch (\Exception $e) {
            $driver->quit();
            dd($e);
        }
    }
}
