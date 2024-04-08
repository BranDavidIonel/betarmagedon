<?php

namespace App\Http\Controllers;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;

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
            $driver->wait(10)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('vue-recycle-scroller__item-view'))
            );

            $matches = $driver->findElements(WebDriverBy::className('vue-recycle-scroller__item-view'));
            $data = [];
            // Iterează prin fiecare element pentru a extrage informațiile despre echipe și cote
            foreach ($matches as $match) {
                $teamName1Element = $match->findElement(WebDriverBy::xpath('.//a/div/div[1]/div[1]/span'));
                $teamName1 = $teamName1Element->getText();
        
                $teamName2Element = $match->findElement(WebDriverBy::xpath('.//a/div/div[1]/div[2]/span'));
                $teamName2 = $teamName2Element->getText();

                $key = "$teamName1-$teamName2";
                
                // Adăugare echipe și cote în array
                $data[$key] = "cote ";
            }

            // Închide driverul WebDriver
            $driver->quit();
            dd($data);
            

            // Returnare view cu datele obținute
            return view('selenium', ['data' => $data]);
        } catch (\Exception $e) {
            $driver->quit();
            dd($e);
        }
    }
}
