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
use App\Models\LinksSearchPage;
use App\Models\Competition;

use App\Services\DateConversionService;
use App\Helpers\StringHelper;

class LinksSitesController extends Controller
{
    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adress Selenium Server
    

    public function getLinksForSuberbet(){
        //sds-icon sds-icon--md sds-icon-sports-soccer
        $detailsSite = SitesSearch::where('name', 'superbet')->first();
        if(empty($detailsSite)){
            echo "No data in SitesSearch table about betano!";
            return false;
        }
        $searchSiteUrl = $detailsSite->link_home_page;
        $idSite = $detailsSite->id;
        $waitTimeout = 10;
        $driver = $this->initializeWebDriver();
        $linksLeague = [];
        try {
            $driver->get($searchSiteUrl);
            $this->waitForPageReady($driver);
            sleep(2);
            // Try to click the button identified by the text "Accepta" cookies accept
            try {
                $acceptButton = $driver->findElement(WebDriverBy::xpath("//button[text()='Accepta']"));
                $acceptButton->click(); // Click the "Accepta" button
            } catch (\Exception $e) {
                // If the button doesn't exist, simply log or handle the situation
                echo "The 'Accepta' button was not found. Proceeding without clicking it.";
            }
            sleep(3);
            $logoLink = $driver->findElement(WebDriverBy::cssSelector('.header-logo a'));
            $logoLink->click(); // Click the link
            //$driver->quit();
            sleep(2);
            $driver = $this->initializeWebDriver();  
            $driver->get($searchSiteUrl);
            $this->waitForPageReady($driver);
            sleep(2);
            
            // $fotbalButton = $driver->findElement(WebDriverBy::xpath("//button[contains(., 'Fotbal')]"));
            // $fotbalButton->click();
            
            $driver->quit();
            dd('ok');
        }catch (\Exception $e) {
            $driver->quit();   
            dd($e);
        
        }finally {
            if (isset($driver)) {
                $driver->quit();
            }
        }  

    }

    public function getLinksForBetano(){
        $detailsSite = SitesSearch::where('name', 'betano')->first();
        if(empty($detailsSite)){
            echo "No data in SitesSearch table about betano!";
            return false;
        }
        $searchSiteUrl = $detailsSite->link_home_page;
        $idSite = $detailsSite->id;
        $waitTimeout = 10;
        // $firefoxOptions = new FirefoxOptions();
        // $argumentsBrowser = [
        //     '--disable-gpu', // Evită problemele cu GPU
        //     '--no-sandbox',  // Necesitat pentru medii de container
        //     '--disable-dev-shm-usage', // Evită problemele cu memoria partajată
        //     '--window-size=1920x1080', // Setează dimensiunea fereastrei pentru vizualizare mai bună
        //     '--remote-debugging-port=5900' // Deschide un port pentru debugging remote
        // ];
        // //$argumentsBrowser = ['--headless'];
        // $firefoxOptions->addArguments($argumentsBrowser); 
        
        // $capabilities = DesiredCapabilities::firefox();
        // $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());
        $driver = $this->initializeWebDriver();
        
        $linksLeague = [];
        try {
            $driver->get($searchSiteUrl);
            sleep(1);
            //wait until the page is ready
            $this->waitForPageReady($driver);
            
            // $driver->wait($waitTimeout)->until(
            //     WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('GTM-sidebar_FOOT'))
            // );
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
            $driver = RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);  
            $driver->get($linkFootbal);
            //$driver->navigate()->refresh();
            $driver->wait($waitTimeout)->until(
                function ($driver) {
                    return $driver->executeScript('return document.readyState') === 'complete';
                }
            );
            //get a error about pop up modal)
            $this->closeSomePromotionPopUpBetano($driver);
            //$svgElements = $driver->findElements(WebDriverBy::xpath("//div[@class='tw-flex tw-items-center tw-cursor-pointer']/svg"));//svg don't work
            ////section[2]/div[4]/div[2]/section/div/div/div/div/div[2]/div[2]/div/div[2]/div[5]/div/div[@class='tw-flex tw-items-center tw-cursor-pointer']
            $svgElements = $driver->findElements(WebDriverBy::xpath("//div[2]/div/div[2]/div/div/div[@class='tw-flex tw-items-center tw-cursor-pointer']"));

            //click for collapse matches 
            foreach ($svgElements as $index => $svgElement) {
                if($index == 0){
                    continue;//first is enabled ( if i clicked become disabled)
                }
                try {
                    // $scrollHeight = $driver->executeScript('return document.body.scrollHeight;');
                    // $scrollDistance = $scrollHeight / 15; // Scrolează pe a 15 parte (putin) din înălțimea paginii
                    // $driver->executeScript("window.scrollTo(0, {$scrollDistance});");
                    $svgElement->click();
                } catch (Exception $e) {
                    echo "Failed to click on SVG element $index: " . $e->getMessage() . "\n";
                }
            }
            sleep(1);
            $currentURL = $driver->getCurrentURL();
            $pageSource = $driver->getPageSource();
            $allFootBallLinks = [];
            $linksLeagueElements = $driver->findElements(WebDriverBy::xpath("//div[contains(@class,'content')]/div/div[@class='tw-flex tw-items-center tw-h-l']/a"));
            foreach($linksLeagueElements as $linkElement){
                $linkleagueUrl = $linkElement->getAttribute('href');
                // Split the URL by "/"
                $parts = explode('/', $linkleagueUrl);
                // Get the 4th part which is the league name (index 3)
                $leagueName = $parts[4]; // Note: arrays in PHP are 0-indexed
                $leagueName = ucwords(str_replace('-', ' ', $leagueName));
                $allFootBallLinks[] = [ 'leagueName' => $leagueName , 'link' => $searchSiteUrl.$linkleagueUrl];
            }

            $driver->quit();
            foreach($allFootBallLinks as $dataLink){
                $leagueName = $dataLink['leagueName'];
                $link = $dataLink['link'];
                $this->insertLinkIfNotExists($idSite,'football',$link, $leagueName) ;
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
    private function initializeWebDriver() 
    {
        // Create a new FirefoxOptions instance
        $firefoxOptions = new FirefoxOptions();
    
        // Define the browser arguments
        $argumentsBrowser = [
            '--disable-gpu', // Avoid GPU issues
            '--no-sandbox',  // Required for containerized environments
            '--disable-dev-shm-usage', // Avoid shared memory issues
            '--window-size=1920x1080', // Set window size for better visualization
            '--remote-debugging-port=5900' // Open a port for remote debugging
        ];
    
        // Add the arguments to Firefox options
        $firefoxOptions->addArguments($argumentsBrowser);
    
        // Create a new instance of DesiredCapabilities for Firefox
        $capabilities = DesiredCapabilities::firefox();
        
        // Set the Firefox options to the capabilities
        $capabilities->setCapability('moz:firefoxOptions', $firefoxOptions->toArray());
    
        // Initialize and return the RemoteWebDriver instance
        return RemoteWebDriver::create(self::SERVER_SELENIUM_URL, $capabilities);
    }
    private function waitForPageReady($driver, $waitTimeout = 5){
        $driver->wait($waitTimeout)->until(
            function ($driver) {
                return $driver->executeScript('return document.readyState') === 'complete';
            }
        );
    }
    private function closeSomePromotionPopUpBetano($driver){
        try {
            $modalCloseButton = $driver->findElement(WebDriverBy::cssSelector('.sb-modal__close__btn.uk-modal-close-default.uk-icon.uk-close'));
            $modalCloseButton->click();
            sleep(1);
        } catch (Exception $e) {
            echo "Nu s-a găsit butonul de închidere al modalului: " . $e->getMessage();
        }
    }
    //I need tot put this function in services
    private function insertLinkIfNotExists($idSite, $typeGame, $linkLeague, $competitionName)
    {
        $competition = $this->findOrCreateCompetition($competitionName);
        // Check if a record with the same values already exists
        $existingLink = LinksSearchPage::where('link_league', $linkLeague)->first();
        // If it doesn't exist, insert the data
        if (!$existingLink) {
            return LinksSearchPage::create([
                'id_site' => $idSite,
                'type_game' => $typeGame,
                'link_league' => $linkLeague,
                'with_data' => false,
                'competition_id' => $competition->id,
            ]);
        }
    
        // If it exists, return a message or the existing record
        return "Link already exists!";
    }
    private function findOrCreateCompetition($competitionName)
    {
        // Search for the competition by name in the competitions table
        $competition = Competition::where('name', $competitionName)->first();

        // If the competition does not exist, create and insert it
        if (!$competition) {
            $competition = Competition::create([
                'name' => $competitionName,
                'alias' => json_encode([$competitionName]) //trebuie sa schimb ca prea simplu ii lasat aici 
            ]);
        }

        // Return the competition instance (whether found or created)
        return $competition;
    }

}
