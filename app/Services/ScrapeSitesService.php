<?php

namespace App\Services;

use App\Models\LinksSearchPage;
use Carbon\Carbon;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Facades\Log;
use Exception;

class ScrapeSitesService
{
    //region init
    private $configWebDriverService;
    public function __construct(ConfigWebDriverService $configWebDriverService)
    {
        $this->configWebDriverService = $configWebDriverService;
    }
    //endregion
    //region betano
    // e.g., "https://ro.betano.com/sport/fotbal/romania/liga-1/17088/"
    public function scrapeBetanoWithScriptMethod($urlSearchMatches)
    {
        $dataReturn = [];
        // Creare un nou driver WebDriver pentru Selenium
        $driver = $this->configWebDriverService->initializeWebDriver();
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
            foreach($matchesDataFromScripts as $matchScript){
                $betDetails = [
                    'team1Name' => '',
                    'team2Name' => '',
                    'odds' => [ '1' => '', 'x' => '', '2' => ''],
                    'startTime' => '',
                    'isLive' => '',
                    'urlSearch' => $urlSearchMatches
                ];

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

                $betDetails['odds']['1'] = $detailsBet1;
                $betDetails['odds']['x'] = $detailsBetx;
                $betDetails['odds']['2'] = $detailsBet2;

                $key = "$teamName1-$teamName2";
                $dataReturn[$key] = $betDetails;
            }

        }catch (\Exception $e) {
            Log::error('eroare scrapeBetanoWithScriptMethod',$e->getTrace());
            echo "A apărut o eroare scrapeBetanoWithScriptMethod: " . $e->getMessage(). "linia: ".$e->getLine();
            $driver->quit();
            exit;
        }finally {
            $driver->quit();
        }
        return $dataReturn;
    }
    //endregion
    //region superbet
    public function scrapeSuperbetWithClassNameMethod($urlSearchMatches){
        $driver = $this->configWebDriverService->initializeWebDriver();
        $superbetMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            //don't work without this ( check is page ready like above)
            $driver->wait(3)->until(
                WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(WebDriverBy::className('single-event'))
            );
            $this->closeModalIfExists_superbet($driver);
            //events-by-date , is card with multiples matches group on date
            $cardDatesElements = $driver->findElements(WebDriverBy::className('event-by-date'));
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
                        //for live matches i have this error
//                        Log::error("eroare superbet");
//                        Log::error($e->getMessage());
                        continue; //next match
                    }
                    $teamName1Element = $teamsElements[0];
                    $teamName1 = $teamName1Element->getText();

                    $teamName2Element = $teamsElements[1];
                    $teamName2 = $teamName2Element->getText();

                    $key = "$teamName1-$teamName2";

                    $betDetails = [
                        'team1Name' => '',
                        'team2Name' => '',
                        'odds' => [ '1' => '', 'x' => '', '2' => ''],
                        'startTime' => '',
                        'isLive' => '',
                        'urlSearch' => $urlSearchMatches
                    ];
                    $betDetails['team1Name'] = $teamName1;
                    $betDetails['team2Name'] = $teamName2;
                    $test = $hourMinutesElement->getText();
                    $stringHourMinutes = $hourMinutesElement->getText();
                    $stringHourMinutes = substr($stringHourMinutes, strpos($stringHourMinutes, ',')+1);
                    list($hour, $minutes) = explode(':', trim($stringHourMinutes));

                    $convertedDate = DateConversionService::convertDateROtoCarbon_superbet($dateFormatRo);

                    $betDetails['startTime'] = $convertedDate->setTime(intval($hour), intval($minutes), 0)->addHours(3)->format('d-m-Y H:i');
                    $betDetails['isLive'] = false;

                    $detailsBetElements = $match->findElements(WebDriverBy::className('e2e-odd-current-value'));
                    if(!empty($detailsBetElements)){
                        $detailsBet1 = $detailsBetElements[0]->getText();
                        $detailsBetx = $detailsBetElements[1]->getText();
                        $detailsBet2 = $detailsBetElements[2]->getText();

                        $betDetails['odds']['1'] = $detailsBet1;
                        $betDetails['odds']['x'] = $detailsBetx;
                        $betDetails['odds']['2'] = $detailsBet2;
                    }
                    $superbetMatches[$key] = $betDetails;
                }
            }
            return $superbetMatches;
        }catch (\Exception $e) {
            Log::error('eroare scrapeSuperbetWithClassNameMethod',$e->getTrace());
            echo "A apărut o eroare scrapeSuperbetWithClassNameMethod:" . $e->getMessage() ." line: ".$e->getLine();
            $driver->quit();
            dd($e);
            exit;
        } finally {
            $driver->quit();
        }
    }
    private function closeModalIfExists_superbet(RemoteWebDriver $driver)
    {
        // XPath to find the close button
        $xpath = "//button[contains(@class, 'modal-close')]";
        // Wait up to 5 seconds for the button to appear
        $wait = new WebDriverWait($driver, 1);
        try {
            // Try to find the button
            $closeButton = $wait->until(
                WebDriverExpectedCondition::elementToBeClickable(WebDriverBy::xpath($xpath))
            );

            if ($closeButton) {
                $closeButton->click();
                //echo "✅ Close button clicked.\n";
            }
        } catch (\Exception $e) {
            // If the button is not found, do nothing
            //echo "❌ Close button not found, skipping...\n";
        }
    }
    //endregion
    //region casa_pariurilor
    //ex https://www.casapariurilor.ro/pariuri-online/fotbal/fotbal/romania-1
    //I am used scrol to get all matches
    public function scrapeCasaPariurilorWithClassNameMethod($urlSearchMatches){
        $driver = $this->configWebDriverService->initializeWebDriver();
        $casaPariurilorMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            // Call the function to handle cookie consent
            AccepCookiesButtonService::acceptCookiesCasaPariurilor($driver);
            sleep(2);
            //select all from football
            $tabAll = $driver->findElement(WebDriverBy::xpath("//button[contains(text(), ' TOT')]"));
            $tabAll->click();
            sleep(2);
            $scrollHeight1 = $driver->executeScript('return document.body.scrollHeight;') /5;
            $driver->executeScript("window.scrollTo(0, {$scrollHeight1});");
            $matches = $driver->findElements(WebDriverBy::xpath("//a[@data-testing-selector='FixtureCard']"));
            $scrollDistance = $scrollHeight1;
            foreach ($matches as $match) {
                $betDetails = [
                    'team1Name' => '',
                    'team2Name' => '',
                    'odds' => [ '1' => '', 'x' => '', '2' => ''],
                    'startTime' => '',
                    'isLive' => '',
                    'urlSearch' => $urlSearchMatches
                ];

                $scrollDistance += $scrollDistance / 10; // 10 parts from total scroll down
                $driver->executeScript("window.scrollTo(0, {$scrollDistance});");
                sleep(1);

                $teamNamesElements = $match->findElements(WebDriverBy::className('fixture-card__participant-name'));
                $teamName1 = $teamNamesElements[0]->getText();
                $teamName2 = $teamNamesElements[1]->getText();

                $key = "$teamName1-$teamName2";
                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;
                //with css method
//                $cssSelectorBet1 = "section.fixture-card__market > div.fixture-card__market-outcomes > button:nth-child(1) > span.odds-button__value";
//                $cssSelectorBet2 = "section.fixture-card__market > div.fixture-card__market-outcomes > button:nth-child(2) > span.odds-button__value";
//                $cssSelectorBet3 = "section.fixture-card__market > div.fixture-card__market-outcomes > button:nth-child(3) > span.odds-button__value";
                try {
                    $xpathBet1 = ".//section[1][contains(@class, 'fixture-card__market')]//button[1]//span[contains(@class, 'odds-button__value')]";
                    $xpathBet2 = ".//section[1][contains(@class, 'fixture-card__market')]//button[2]//span[contains(@class, 'odds-button__value')]";
                    $xpathBet3 = ".//section[1][contains(@class, 'fixture-card__market')]//button[3]//span[contains(@class, 'odds-button__value')]";
                    $elementBet1 = $match->findElement(WebDriverBy::xpath($xpathBet1));
                    $elementBetx = $match->findElement(WebDriverBy::xpath($xpathBet2));
                    $elementBet2 = $match->findElement(WebDriverBy::xpath($xpathBet3));
                    $elementDateTime = $match->findElement(WebDriverBy::xpath(".//div/time"));
                }catch (\Exception $e){
                    Log::error("Error in casapariorilor to match $key ,other details -> " . $e->getMessage(), [
                        'exception' => $e,
                        'xpathBet1' => $xpathBet1,
                        'xpathBet2' => $xpathBet2,
                        'xpathBet3' => $xpathBet3,
                    ]);
                    continue;
                }

                if(!empty($elementBet1 && !empty($elementBetx) && !empty($elementBet2) && !empty($elementDateTime))){
                    $detailsBet1 = $elementBet1->getText();
                    $detailsBetx = $elementBetx->getText();
                    $detailsBet2 = $elementBet2->getText();
                    $dateTime = $elementDateTime->getText();

                    $betDetails['odds']['1'] = $detailsBet1;
                    $betDetails['odds']['x'] = $detailsBetx;
                    $betDetails['odds']['2'] = $detailsBet2;

                    $betDetails['startTime'] = DateConversionService::convertDate_CasaPariurilor($dateTime);

                }else{
                    throw new \Exception("Error: Missing required betting elements or match start time.");
                }
                $casaPariurilorMatches[$key] = $betDetails;
            }
        }catch (\Exception $e) {
            //Log::error('eroare scrapeCasaPariurilorWithClassNameMethod',$e->getTrace());
            echo "A apărut o eroare superbet functia cautare scrapeCasaPariurilorWithClassNameMethod:" . $e->getMessage().' linia:'.$e->getLine();
            $driver->quit();
            //throw new \Exception('eroare scrapeCasaPariurilorWithClassNameMethod',$e->getTrace());
            $errorMessage = "A apărut o eroare în scrapeCasaPariurilorWithClassNameMethod: " .
                $e->getMessage() . ' linia:' . $e->getLine();

            throw new \Exception($errorMessage, $e->getCode());
            //exit;
        }finally {
            $driver->quit();
        }
        return $casaPariurilorMatches;
    }

    //endregion
}
