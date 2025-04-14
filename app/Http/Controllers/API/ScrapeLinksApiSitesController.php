<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SitesSearch;
use App\Services\AccepCookiesButtonService;
use App\Services\CheckDataService;
use App\Services\ConfigWebDriverService;
use App\Services\SaveLinkService;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

//my class

class ScrapeLinksApiSitesController extends Controller
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
        $driver = $this->configWebDriverService->initializeWebDriver();
        $allFootBallLinks = [];
        try {
            $testSearch = $searchSiteUrl."/sport/fotbal/";
            $driver->get($testSearch);
            $this->configWebDriverService->waitForPageReady($driver);
            sleep(2);
            $pageSource = $driver->getPageSource();
            //find a script with all data about links
            $pattern = '/window\["initial_state"\]\s*=\s*(.*?)\s*}<\/script>/s';
            preg_match($pattern, $pageSource, $matches);
            $scriptContent = [];
            if (isset($matches[1])) {
                $initialStateJson = $matches[1]."}";
                $scriptContent = json_decode($initialStateJson, true);
            }
            $driver->quit();
            $linksLeagues = [];
            $regionGroupsData = Arr::get($scriptContent,'data.regionGroups',[]);
            foreach ($regionGroupsData as $regionGroupData) {
                $countryData = Arr::get($regionGroupData, "regions", []);
                foreach ($countryData as $country) {

                    $leagues = Arr::get($country, "leagues", []);
                    foreach ($leagues as $league) {
                        $linkLeagueFromScript = Arr::get($league, "url");
                        $linksLeagues[] = $linkLeagueFromScript;
                    }
                }
            }
            foreach($linksLeagues as $linkLeagueUrl){
                // Split the URL by "/"
                $parts = explode('/', $linkLeagueUrl);
                // Get the 4th part which is the league name , 3 -> country name
                $leagueName = $parts[4];
                $leagueName = trim(str_replace('-', ' ', $leagueName));
                $countryName = $parts[3];
                $countryName = trim(str_replace('-', ' ', $countryName));
                if(!$this->checkDataService->checkCountryExist($countryName)){
                    $countryName = null;
                }
                //have "\" I want to remove
                //$linkLeagueUrl = str_replace('\\/', '/', $linkLeagueUrl);
                $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkLeagueUrl, 'countryName' => $countryName];
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
            // Return the results as JSON
            return response()->json([
                'success' => true,
                'data' => $allFootBallLinks,
            ], 200, [], JSON_UNESCAPED_SLASHES);
        }catch (\Exception $e) {
            $driver->quit();
            dd($e);
        }finally {
            $driver->quit();
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
            sleep(3);
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
            return response()->json([
                'success' => true,
                'data' => $allFootBallLinks,
            ], 200, [], JSON_UNESCAPED_SLASHES);

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
            return response()->json([
                'success' => true,
                'data' => $allFootBallLinks,
            ], 200, [], JSON_UNESCAPED_SLASHES);
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
