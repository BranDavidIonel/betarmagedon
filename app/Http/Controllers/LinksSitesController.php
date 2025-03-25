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
            //close pop up modal)
            $this->closeSomePromotionPopUpBetano($driver);

            $buttonFootbal = $driver->findElement(WebDriverBy::xpath("//div[1]/li/div/div[contains(@class, 'sport-picker__item__inline')]/a"));

            $linkFootbal = $buttonFootbal->getAttribute('href');
            $linkFootbal =  $searchSiteUrl .$linkFootbal;
            sleep(1);
            $driver->quit();
            $driver = $this->configWebDriverService->initializeWebDriver();
            $driver->get($linkFootbal);
            $this->configWebDriverService->waitForPageReady($driver);

            $this->closeSomePromotionPopUpBetano($driver);
            $svgElements = $driver->findElements(WebDriverBy::xpath("//div/div/div[2]/div/div/div[contains(@class,'tw-flex tw-items-center tw-cursor-pointer')]"));

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

            $linksLeagueElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class,'tw-pt-0 content')]/div//..//a"));
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
            $driver->quit();
        }
    }

    private function closeSomePromotionPopUpBetano($driver){
        try {
            $buttonPromotional = $driver->findElement(WebDriverBy::xpath("//div/button[contains(@class, 'modal-close-default')]"));
            $buttonPromotional->click(); // Click close modal
        } catch (\Exception $e) {
            // If the button doesn't exist, simply log or handle the situation
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
            $logoLink = $driver->findElement(WebDriverBy::cssSelector('.header-logo a'));
            $logoLink->click(); // Click the link
            sleep(3);
            $fotbalButton = $driver->findElement(WebDriverBy::xpath("//div[contains(@class, 'sds-sidebar')]/..//a[1]/div/div[contains(text(), 'Fotbal')]"));
            $fotbalButton->click();
            sleep(2);
            //close some modal
            $buttonModal = $driver->findElement(WebDriverBy::xpath("//button[contains(@class, 'modal-close')]"));
            $buttonModal->click(); //close modal
            try {
                $competitiButton = $driver->findElement(WebDriverBy::xpath("//button[div[contains(text(), 'Competiții')]]"));
                $competitiButton->click(); // Click the "Competiti" tab
            } catch (\Exception $e) {
                throw new \Exception("The 'Competiti' tab button was not found. Proceeding without clicking it.");
            }
            sleep(1);
            //toggle icons buttons
            $iconsCountryButtonsElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class, 'competition-category')]/div/i"));
            $countCountryButtonsElements = count($iconsCountryButtonsElements);
            $iterationDivCountryElement = 1;
            while($iterationDivCountryElement <= $countCountryButtonsElements){
                sleep(1);
                $countryNameElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div[contains(@class,'competition-category')]//span[contains(@class, 'category-name')]"));
                $countryName = $countryNameElement->getText();
                $countryName = strtolower($countryName);

                $iconCountryButtonElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div[contains(@class, 'competition-category')]/div/i"));
                $iconCountryButtonElement->click();
                sleep(1);

                $iconsLeagueButtonsElements = $driver->findElements(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div/main/a[contains(@class, 'tournament') and contains(@class, 'e2e-competition-tournament')]"));
                $countLeagueButtonsElements = count($iconsLeagueButtonsElements);
                $iterationDivLeagueElementElement = 1;
                while($iterationDivLeagueElementElement <= $countLeagueButtonsElements){
                    $leagueLinkElement = $driver->findElement(WebDriverBy::xpath("//div[$iterationDivCountryElement]/div/main/a[$iterationDivLeagueElementElement][contains(@class,'tournament')]"));
                    $linkLeagueUrl = $leagueLinkElement->getAttribute('href');

                    $leagueNameElement = $leagueLinkElement->findElement(WebDriverBy::xpath(".//div[@class='tournament-name']"));
                    $leagueName = strtolower($leagueNameElement->getText());

                    $iterationDivLeagueElementElement++;
                    $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkLeagueUrl, 'countryName' => $countryName];

                }
                $iterationDivCountryElement++;
            }

            //insert data
            foreach($allFootBallLinks as $dataLink){
                $leagueName = $dataLink['leagueName'];
                $link = $dataLink['link'];
                $countryName = $dataLink['countryName'];
                //$this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
                $this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
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
            //expand football leagues
            $footballButton = $driver->findElement(WebDriverBy::xpath("//li[3]/a/div[contains(text(), 'Fotbal')]"));
            $footballButton->click();
            sleep(2);
            //select all from football
            $tabAll = $driver->findElement(WebDriverBy::xpath("//button[contains(text(), ' TOT')]"));
            $tabAll->click();
            sleep(2);
            $countriesElements = $driver->findElements(WebDriverBy::xpath("//li[contains(@class, 'bg-odd-button-default')]/ul/li"));
            foreach ($countriesElements as $countryElement) {
                $countryTitleElement = $countryElement->findElement(WebDriverBy::xpath(".//a/div[contains(@class, ' side-menu-item__title--single-line')]"));
                $countryName = $countryTitleElement->getText();
                $countryName = strtolower(removeRomanianDiacritics($countryName));
                $countryTitleElement->click();
                sleep(1);
                $linksLeagueElements = $driver->findElements(WebDriverBy::xpath("//li//a[contains(@class, 'tournament-safe-link')]"));
                foreach($linksLeagueElements as $linkElement){
                    $linkLeagueUrl = $linkElement->getAttribute('href');
                    $leagueName = $linkElement->findElement(WebDriverBy::xpath(".//div[contains(@class, 'side-menu-item__title')]"))->getText();
                    $leagueName = strtolower(removeRomanianDiacritics($leagueName));

                    $allFootBallLinks[] = [
                        'leagueName' => $leagueName ,
                        'link' => $searchSiteUrl.$linkLeagueUrl,
                        'countryName' => $countryName
                    ];
                }
            }
            $driver->quit();
            //insert data
//            foreach($allFootBallLinks as $dataLink){
//                $leagueName = $dataLink['leagueName'];
//                $link = $dataLink['link'];
//                $countryName = $dataLink['countryName'];
//                //$this->saveLinkService->createScrapedCompetition($idSite, $leagueName, $countryName);
//                //$this->saveLinkService->insertLinkIfNotExists($idSite,'football',$link, $leagueName, $countryName) ;
//            }
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
