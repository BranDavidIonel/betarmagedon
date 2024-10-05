<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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
use App\Models\SitesSearch;
use App\Services\DateConversionService;
use App\Services\SaveLinkService;
use App\Services\ConfigWebDriverService;
use App\Services\AccepCookiesButtonService;
use App\Services\CheckDataService;

class LinksSitesController extends Controller
{
    //region index main data
    private SaveLinkService $saveLinkService;
    private ConfigWebDriverService $configWebDriverService;
    private  CheckDataService $checkDataService;
    public function __construct(SaveLinkService $linkService, ConfigWebDriverService $configWebDriverService, CheckDataService $checkDataService)
    {
        $this->saveLinkService = $linkService;
        $this->configWebDriverService = $configWebDriverService;
        $this->checkDataService = $checkDataService;
    }
    //endregion
    //region betano
    public function getLinksForBetano(){
        $detailsSite = SitesSearch::where('name', 'betano')->first();
        if(empty($detailsSite)){
            echo "No data in SitesSearch table about betano!";
            return false;
        }
        $searchSiteUrl = $detailsSite->link_home_page;
        $idSite = $detailsSite->id;
        $driver = $this->configWebDriverService->initializeWebDriver();

        $allFootBallLinks = [];
        try {
            $driver->get($searchSiteUrl);
            sleep(1);
            $this->configWebDriverService->waitForPageReady($driver);
            // Identificăm și închidem modalul dacă apare ( get a error about pop up modal)
            $this->closeSomePromotionPopUpBetano($driver);

            $buttonFootbal = $driver->findElement(WebDriverBy::xpath("//div[contains(@class, 'sport-picker__item__inline')]/a"));
            //$buttonFootbal->click();
            // Navigăm direct către URL-ul obținut
            $linkFootbal = $buttonFootbal->getAttribute('href');
            $linkFootbal =  $searchSiteUrl .$linkFootbal;
            sleep(1);
            //$driver->navigate($linkFootbal)->refresh();
            $driver->quit();
            $driver = $this->configWebDriverService->initializeWebDriver();
            $driver->get($linkFootbal);
            $this->configWebDriverService->waitForPageReady($driver);
            //get a error about pop up modal)
            $this->closeSomePromotionPopUpBetano($driver);
            $svgElements = $driver->findElements(WebDriverBy::xpath("//div/div[2]/div/div/div[@class='tw-flex tw-items-center tw-cursor-pointer']"));

            //click for collapse matches
            foreach ($svgElements as $index => $svgElement) {
                if($index == 0){
                    continue;//first is enabled ( if i clicked become disabled)
                }
                try {
                    $svgElement->click();
                } catch (\Exception $e) {
                    echo "Failed to click on SVG element $index: " . $e->getMessage() . "\n";
                }
            }
            sleep(1);
            $currentURL = $driver->getCurrentURL();
            $pageSource = $driver->getPageSource();

            $linksLeagueElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class,'content')]/div/div[@class='tw-flex tw-items-center tw-h-l']/a"));
            foreach($linksLeagueElements as $linkElement){
                $linkleagueUrl = $linkElement->getAttribute('href');
                // Split the URL by "/"
                $parts = explode('/', $linkleagueUrl);
                // Get the 4th part which is the league name , 3 -> country name
                $leagueName = $parts[4];
                $leagueName = trim(str_replace('-', ' ', $leagueName));
                $countryName = $parts[3];
                $countryName = trim(str_replace('-', ' ', $countryName));
                if(!$this->checkDataService->checkCountryExist($countryName)){
                    $countryName = null;
                }

                $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkleagueUrl, 'countryName' => $countryName];
            }

            $driver->quit();
            //insert data
            foreach($allFootBallLinks as $dataLink){
                $leagueName = $dataLink['leagueName'];
                $link = $dataLink['link'];
                $countryName = $dataLink['countryName'];
                //$this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
                //$this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
            }
            dd($allFootBallLinks);
        }catch (\Exception $e) {
            $driver->quit();
            dd($e);
        }finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }

    private function closeSomePromotionPopUpBetano($driver){
        try {
            $modalCloseButton = $driver->findElement(WebDriverBy::cssSelector('.sb-modal__close__btn.uk-modal-close-default.uk-icon.uk-close'));
            $modalCloseButton->click();
            sleep(1);
        } catch (\Exception $e) {
            Log::info("Error in closeSomePromotionPopUpBetano:".$e->getMessage(), $e->getTrace());
        }
    }
    //endregion
    //region superbet
    //get data from 'competitii tab'
    public function getLinksForSuperbet(){
        //sds-icon sds-icon--md sds-icon-sports-soccer
        $detailsSite = SitesSearch::where('name', 'superbet')->first();
        if(empty($detailsSite)){
            echo "No data in SitesSearch table about superbet!";
            return false;
        }
        $searchSiteUrl = $detailsSite->link_home_page;
        $idSite = $detailsSite->id;
        $driver = $this->configWebDriverService->initializeWebDriver();
        $allFootBallLinks = [];
        try {
            $driver->get($searchSiteUrl);
            $this->configWebDriverService->waitForPageReady($driver);
            sleep(2);
            // Try to click the button identified by the text "Acceptați" cookies accept
            try {
                $acceptButton = $driver->findElement(WebDriverBy::xpath("//button[contains(text(), 'Acceptați')]"));
                $acceptButton->click(); // Click the "Accepta" button
            } catch (\Exception $e) {
                // If the button doesn't exist, simply log or handle the situation
                throw new \Exception("The 'Accepta' button was not found. Proceeding without clicking it.");
            }
            sleep(1);
            $logoLink = $driver->findElement(WebDriverBy::cssSelector('.header-logo a'));
            $logoLink->click(); // Click the link
            sleep(1);
            $fotbalButton = $driver->findElement(WebDriverBy::xpath("//button[contains(., 'Fotbal')]"));
            $fotbalButton->click();
            sleep(1);
            try {
                $competitiButton = $driver->findElement(WebDriverBy::xpath("//button[span[contains(text(), 'Competiții')]]"));
                $competitiButton->click(); // Click the "Competiti" tab
            } catch (\Exception $e) {
                throw new \Exception("The 'Competiti' tab button was not found. Proceeding without clicking it.");
            }
            sleep(1);
            //toggle icons buttons
            $iconsCountryButtonsElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class, 'toggle-icon-container')]/i"));
            $countCountryButtonsElements = count($iconsCountryButtonsElements);
            $iterationDivCountryElement = 1;
            //$countCountryButtonsElements = 3;//debug
            //$previewNrLeague = 0;
            while($iterationDivCountryElement <= $countCountryButtonsElements){
                $countryNameElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div[@class='competition-category']//span[contains(@class, 'category-name')]"));
                $countryName = $countryNameElement->getText();
                $countryName = strtolower($countryName);

                $iconCountryButtonElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div/div[contains(@class, 'toggle-icon-container')]/i"));
                $iconCountryButtonElement->click();
                //$this->configWebDriverService->scrollDownCustom($driver, ceil($iterationDivCountryElement -1 / 2), 100);
                sleep(1);

                $iconsLeagueButtonsElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class, 'tournament') and contains(@class, 'e2e-competition-tournament')]/div/i"));
                $countLeagueButtonsElements = count($iconsLeagueButtonsElements);
                $iterationDivLeagueElementElement = 1;
                while($iterationDivLeagueElementElement <= $countLeagueButtonsElements){
                    $leagueNameElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div/main/div[$iterationDivLeagueElementElement][contains(@class,'tournament')]//div[@class='tournament-name']"));
                    $leagueName = $leagueNameElement->getText();
                    $leagueName = strtolower($leagueName);

                    $iconsLeagueButtonsElements = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivLeagueElementElement][contains(@class, 'tournament') and contains(@class, 'e2e-competition-tournament')]/div/i"));
                    $iconsLeagueButtonsElements->click();
                    $linkLeagueUrl = $driver->getCurrentURL();
                    $driver->navigate()->back();
                    sleep(1);
                    $iterationDivLeagueElementElement++;
                    $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkLeagueUrl, 'countryName' => $countryName];
                    if($iterationDivLeagueElementElement <= $countLeagueButtonsElements) {
                        $iconCountryButtonElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div/div[contains(@class, 'toggle-icon-container')]/i"));
                        $iconCountryButtonElement->click();
                    }
                }

                $iterationDivCountryElement++;
            }

            sleep(2);

            //insert data
            foreach($allFootBallLinks as $dataLink){
                $leagueName = $dataLink['leagueName'];
                $link = $dataLink['link'];
                $countryName = $dataLink['countryName'];
                //$this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
                //$this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
            }
            $driver->quit();
            dd($allFootBallLinks);

        }catch (\Exception $e) {
            $driver->quit();
            dd($e);

        }finally {
            $driver->quit();
        }
    }


    //region old function
    //get data from 'calendar' tab a lot of data (from 'competitii' tab is better)
//    public function getLinksForSuperbetFromCalendar(){
//        //sds-icon sds-icon--md sds-icon-sports-soccer
//        $detailsSite = SitesSearch::where('name', 'superbet')->first();
//        if(empty($detailsSite)){
//            echo "No data in SitesSearch table about superbet!";
//            return false;
//        }
//        $searchSiteUrl = $detailsSite->link_home_page;
//        $idSite = $detailsSite->id;
//        $driver = $this->configWebDriverService->initializeWebDriver();
//        $allFootBallLinks = [];
//        try {
//            $driver->get($searchSiteUrl);
//            $this->configWebDriverService->waitForPageReady($driver);
//            sleep(2);
//            // Try to click the button identified by the text "Accepta" cookies accept
//            try {
//                $acceptButton = $driver->findElement(WebDriverBy::xpath("//button[contains(text(), 'Acceptați')]"));
//                $acceptButton->click(); // Click the "Accepta" button
//            } catch (\Exception $e) {
//                // If the button doesn't exist, simply log or handle the situation
//                echo "The 'Accepta' button was not found. Proceeding without clicking it.";
//            }
//            sleep(3);
//            $logoLink = $driver->findElement(WebDriverBy::cssSelector('.header-logo a'));
//            $logoLink->click(); // Click the link
//            sleep(2);
//            $fotbalButton = $driver->findElement(WebDriverBy::xpath("//button[contains(., 'Fotbal')]"));
//            $fotbalButton->click();
//            sleep(2);
//            // Find the button with the text "Toate" using XPath
//            $buttonAll = $driver->findElement(
//                WebDriverBy::xpath("//button[contains(text(), 'Toate')]")
//            );
//            $buttonAll->click();
//            sleep(1);
//            $uniqueLinks = [];
//            // Perform slow scrolls with a slight pause
//            for ($i = 0; $i < 195; $i++) {
//                // Execute JavaScript to scroll down by 800 pixels each time
//                $driver->executeScript('window.scrollBy(0, 700);');
//                // Sleep for 0.5 seconds between each scroll to make it smooth
//                usleep(500000); // 500,000 microseconds = 0.5 seconds
//                // Find all <a> elements inside the div with class 'group-header'
//                $linkElements = $driver->findElements(
//                    WebDriverBy::xpath("//div[contains(@class, 'group-header__wrapper')]//a[contains(@class, 'group-header__details')]")
//                );
//
//                foreach ($linkElements as $linkElement) {
//                    $linkleagueUrl = $linkElement->getAttribute('href');
//                    // Skip this link if it's already in the uniqueLinks array
//                    if (in_array($linkleagueUrl, $uniqueLinks)) {
//                        continue; // Skip duplicate
//                    }
//                    // Add the URL to the uniqueLinks array to prevent duplicates
//                    $uniqueLinks[] = $linkleagueUrl;
//                    // Split the URL by "/"
//                    $parts = explode('/', $linkleagueUrl);
//                    // Get the 4th part which is the league name , 3 -> country name
//
//                    $countryName = $parts[3];
//                    $countryName = trim(str_replace('-', ' ', $countryName));
//                    if(!$this->checkDataService->checkCountryExist($countryName)){
//                        $countryName = null;
//                    }
//                    $leagueName = $parts[4];
//                    $leagueName = trim(str_replace('-', ' ', $leagueName));
//                    $leagueName = trim(str_replace($countryName, ' ', $leagueName));
//                    $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkleagueUrl, 'countryName' => $countryName];
//                }
//            }
//            $driver->quit();
//            //insert data
//            foreach($allFootBallLinks as $dataLink){
//                $leagueName = $dataLink['leagueName'];
//                $link = $dataLink['link'];
//                $countryName = $dataLink['countryName'];
//                $this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
//                //$this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
//            }
//            dd($allFootBallLinks);
//        }catch (\Exception $e) {
//            $driver->quit();
//            dd($e);
//
//        }finally {
//            $driver->quit();
//        }
//    }
    //endregion

    //endregion superbet
    //region casa_pariurilor
    public function getLinksForCasaPariurilor(){
        $detailsSite = SitesSearch::where('name', 'casa_pariurilor')->first();
        if(empty($detailsSite)){
            echo "No data in SitesSearch table about casa_pariurilor!";
            return false;
        }
        $searchSiteUrl = $detailsSite->link_home_page;
        $idSite = $detailsSite->id;
        $driver = $this->configWebDriverService->initializeWebDriver();

        $allFootBallLinks = [];
        try {
            $driver->get($searchSiteUrl);
            sleep(4);
            $this->configWebDriverService->waitForPageReady($driver);
            AccepCookiesButtonService::acceptCookiesCasaPariurilor($driver);
            //get top of the page
            //$driver->executeScript('window.scrollTo(0, 0);');

            $linksLeagueElements = $driver->findElements(WebDriverBy::xpath("//ul/li[4][contains(@class, 'item-sport')]/ul/li[contains(@class, 'item-competition')]/a"));
            foreach($linksLeagueElements as $linkElement){
                $linkleagueUrl = $linkElement->getAttribute('href');
                // Split the URL by "/"
                $parts = explode('/', $linkleagueUrl);
                $countryWithLeagueName = $parts[3];
                $countryWithLeagueNameArray = explode('-', $countryWithLeagueName);
                if(count($countryWithLeagueNameArray) == 1){
                    $leagueName = $countryWithLeagueNameArray[0];
                    $countryName = null;
                }else{
                    $countryName = $countryWithLeagueNameArray[0];
                    if(!$this->checkDataService->checkCountryExist($countryName)){
                        $countryName = null;
                        $leagueName = str_replace( '-', ' ', $countryWithLeagueName);;
                    }else{
                        $countryWithLeagueName = str_replace( '-', ' ', $countryWithLeagueName);
                        $leagueName = str_replace($countryName, '', $countryWithLeagueName);//remove country and remains only league name
                    }
                }
                //nu ii bun inca ce am pe aici
                //$countryName = trim(str_replace('-', ' ', $countryName));
                $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkleagueUrl, 'countryName' => $countryName];
            }

            $driver->quit();
            //insert data
            foreach($allFootBallLinks as $dataLink){
                $leagueName = $dataLink['leagueName'];
                $link = $dataLink['link'];
                $countryName = $dataLink['countryName'];
                //$this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
                //$this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
            }
            dd($allFootBallLinks);
        }catch (\Exception $e) {
            $driver->quit();
            dd($e);
        }finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }
    }

    //endregion


}
