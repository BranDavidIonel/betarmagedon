<?php

namespace App\Services;

use Carbon\Carbon;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Illuminate\Support\Facades\Log;
use Facebook\WebDriver\Exception\TimeoutException;
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
                $initialStateJson = $matches[1] . '}';
                $scriptContent = json_decode($initialStateJson, true);
            }

            $driver->quit();

            $matchesDataFromScripts = isset($scriptContent['data']['blocks']) ? $scriptContent['data']['blocks'][0]['events'] : [];
            foreach ($matchesDataFromScripts as $matchScript) {
                //don't exist the match
                if (! isset($matchScript['participants'][0]['name']) || ! isset($matchScript['participants'][1]['name'])) {
                    continue;
                }

                $betDetails = [
                    'team1Name' => $matchScript['participants'][0]['name'],
                    'team2Name' => $matchScript['participants'][1]['name'],
                    'odds' => ['1' => '', 'x' => '', '2' => ''],
                    'startTime' => '',
                    'isLive' => '',
                    'urlSearch' => $urlSearchMatches,
                ];

                $timestamp = $matchScript['startTime'] / 1000;
                $dateStartMatch = Carbon::createFromTimestamp($timestamp);
                $betDetails['startTime'] = $dateStartMatch->addHours(2)->format('d-m-Y H:i');
                $betDetails['isLive'] = isset($matchScript['liveNow']) ? true : false;
                $detailsBetFromScript = $matchScript['markets'][0]['selections'];
                if (empty($detailsBetFromScript)) {
                    continue; //I need details about 1 | x | 2 teams
                }

                $detailsBet1 = $detailsBetFromScript[0]['price'];
                $detailsBetx = $detailsBetFromScript[1]['price'];
                $detailsBet2 = $detailsBetFromScript[2]['price'];

                $betDetails['odds']['1'] = $detailsBet1;
                $betDetails['odds']['x'] = $detailsBetx;
                $betDetails['odds']['2'] = $detailsBet2;

                $key = "{$betDetails['team1Name']}-{$betDetails['team2Name']}";
                $dataReturn[$key] = $betDetails;
            }

        } catch (\Exception $e) {
            Log::error('eroare scrapeBetanoWithScriptMethod', $e->getTrace());
            echo 'A apărut o eroare scrapeBetanoWithScriptMethod: '.$e->getMessage().'linia: '.$e->getLine();
            $driver->quit();
            exit;
        } finally {
            $driver->quit();
        }

        return $dataReturn;
    }

    //endregion
    //region superbet
    public function scrapeSuperbetWithClassNameMethod($urlSearchMatches)
    {
        $driver = $this->configWebDriverService->initializeWebDriver();
        $superbetMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            //don't work without this ( check is page ready like above)
            // Wait for elements with class "single-event" to be present
            try {
                $driver->wait(3)->until(
                    WebDriverExpectedCondition::presenceOfAllElementsLocatedBy(
                        WebDriverBy::className('single-event')
                    )
                );
            } catch (TimeoutException $e) {
                throw new \Exception("No matches found for URL: {$urlSearchMatches}");
            }
            $this->closeModalIfExists_superbet($driver);
            //events-by-date , is card with multiples matches group on date
            $cardDatesElements = $driver->findElements(WebDriverBy::className('event-by-date'));
            foreach ($cardDatesElements as $cardElement) {
                $dateMatchElement = $cardElement->findElement(WebDriverBy::className('events-date'));
                $dateFormatRo = $dateMatchElement->getText();
                $matches = $cardElement->findElements(WebDriverBy::className('single-event'));

                foreach ($matches as $match) {
                    try {
                        $teamsElements = $match->findElements(WebDriverBy::className('event-competitor__name'));
                        //capitalize -> hour and minutes get string
                        $hourMinutesElement = $match->findElement(WebDriverBy::className('capitalize'));
                    } catch (\Exception $e) {
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
                        'odds' => ['1' => '', 'x' => '', '2' => ''],
                        'startTime' => '',
                        'isLive' => '',
                        'urlSearch' => $urlSearchMatches,
                    ];
                    $betDetails['team1Name'] = $teamName1;
                    $betDetails['team2Name'] = $teamName2;
                    $test = $hourMinutesElement->getText();
                    $stringHourMinutes = $hourMinutesElement->getText();
                    $stringHourMinutes = substr($stringHourMinutes, strpos($stringHourMinutes, ',') + 1);
                    [$hour, $minutes] = explode(':', trim($stringHourMinutes));

                    $convertedDate = DateConversionService::convertDateROtoCarbon_superbet($dateFormatRo);

                    $betDetails['startTime'] = $convertedDate->setTime(intval($hour), intval($minutes), 0)->addHours(2)->format('d-m-Y H:i');
                    $betDetails['isLive'] = false;

                    $detailsBetElements = $match->findElements(WebDriverBy::className('odd-button__odd-value'));
                    if (! empty($detailsBetElements)) {
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
        } catch (\Exception $e) {
            $messageError = $e->getMessage();
            if(strpos($messageError, 'No matches found for URL:') === false) {
                Log::error($messageError);
                Log::error('error getTrace -> ', $e->getTrace());
            }else{
                Log::alert($messageError);
            }
            $driver->quit();
//            dd($e);
            return [];
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
    public function scrapeCasaPariurilorWithClassNameMethod($urlSearchMatches)
    {
        $driver = $this->configWebDriverService->initializeWebDriver();
        $casaPariurilorMatches = [];
        try {
            $driver->get($urlSearchMatches);
            $this->configWebDriverService->waitForPageReady($driver);
            // Call the function to handle cookie consent
            AccepCookiesButtonService::acceptCookiesCasaPariurilor($driver);
            sleep(2);
            //select all from football

            // Scroll to bottom to load all lazy-loaded content
            $totalHeight = $this->scrollToBottomLazyLoad($driver, 30, 1);

            // Scroll up by 50% to start from middle of page
            $this->scrollUpByPercentage($driver, 80);

            $matches = $driver->findElements(WebDriverBy::xpath("//a[@data-testing-selector='FixtureCard']"));
            $scrollDistance = (int) ($totalHeight / 2);
            $maxScrolls = 6;
            $scrollCount = 0;
            foreach ($matches as $keyMatches => $match) {
                $betDetails = [
                    'team1Name' => '',
                    'team2Name' => '',
                    'odds' => ['1' => '', 'x' => '', '2' => ''],
                    'startTime' => '',
                    'isLive' => '',
                    'urlSearch' => $urlSearchMatches,
                ];

                // Scroll incrementally every 3 matches
                $scrollDistance = $this->scrollDownIncrementalByPercentage($driver, $keyMatches, 3, $scrollDistance);

                $team1NameElement = $match->findElement(WebDriverBy::xpath(".//div[1][contains(@class,'fixture-card__participant')]/div"));
                $teamName1 = $team1NameElement->getText();
                $team2NameElement = $match->findElement(WebDriverBy::xpath(".//div[2][contains(@class,'fixture-card__participant')]/div"));
                $teamName2 = $team2NameElement->getText();

                $key = "$teamName1-$teamName2";
                $betDetails['team1Name'] = $teamName1;
                $betDetails['team2Name'] = $teamName2;
                try {
                    $xpathBet1 = ".//section[1]//div[1]/div[contains(@class,'odds-button2__value')][1]";
                    $xpathBet2 = ".//section[1]//div[2]/div[contains(@class,'odds-button2__value')][1]";
                    $xpathBet3 = ".//section[1]//div[3]/div[contains(@class,'odds-button2__value')][1]";
                    $elementBet1 = $match->findElement(WebDriverBy::xpath($xpathBet1));
                    $elementBetx = $match->findElement(WebDriverBy::xpath($xpathBet2));
                    $elementBet2 = $match->findElement(WebDriverBy::xpath($xpathBet3));
                    $elementDateTime = $match->findElement(WebDriverBy::xpath(".//div[contains(@class,'fixture-card__time')]/time"));
                } catch (\Exception $e) {
//                    Log::error("Error in casapariorilor to match $key ,other details -> ".$e->getMessage(), [
//                        'exception' => $e,
//                        'xpathBet1' => $xpathBet1,
//                        'xpathBet2' => $xpathBet2,
//                        'xpathBet3' => $xpathBet3,
//                    ]);

                    continue;
                }

                if (! empty($elementBet1) && ! empty($elementBetx) && ! empty($elementBet2) && ! empty($elementDateTime)) {
                    $detailsBet1 = $elementBet1->getText();
                    $detailsBetx = $elementBetx->getText();
                    $detailsBet2 = $elementBet2->getText();
                    $dateTime = $elementDateTime->getText();

                    $betDetails['odds']['1'] = $detailsBet1;
                    $betDetails['odds']['x'] = $detailsBetx;
                    $betDetails['odds']['2'] = $detailsBet2;

                    $betDetails['startTime'] = DateConversionService::convertDate_CasaPariurilor($dateTime);

                } else {
                    throw new \Exception('Error: Missing required betting elements or match start time.');
                }
                $casaPariurilorMatches[$key] = $betDetails;
            }
        } catch (\Exception $e) {
            //Log::error('eroare scrapeCasaPariurilorWithClassNameMethod',$e->getTrace());
            echo 'A apărut o eroare superbet functia cautare scrapeCasaPariurilorWithClassNameMethod:'.$e->getMessage().' linia:'.$e->getLine();
            $driver->quit();
            //throw new \Exception('eroare scrapeCasaPariurilorWithClassNameMethod',$e->getTrace());
            $errorMessage = 'A apărut o eroare în scrapeCasaPariurilorWithClassNameMethod: '.
                $e->getMessage().' linia:'.$e->getLine();

            throw new \Exception($errorMessage, $e->getCode());
            //exit;
        } finally {
            $driver->quit();
        }

        return $casaPariurilorMatches;
    }

    //endregion

    //region scroll methods
    /**
     * Scrolls to the bottom of the page, waiting for lazy-loaded content to load.
     * Continues scrolling until no new content is detected or max attempts reached.
     *
     * @param RemoteWebDriver $driver
     * @param int $maxScrolls - Maximum number of scroll attempts
     * @param int $waitSeconds - Time to wait between scrolls for content to load
     * @return int - Total page height after scrolling
     */
    private function scrollToBottomLazyLoad(RemoteWebDriver $driver, $maxScrolls = 30, $waitSeconds = 1)
    {
        $lastHeight = 0;
        $scrollCount = 0;

        while ($scrollCount < $maxScrolls) {
            $height = (int) $driver->executeScript('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);');
            $driver->executeScript("window.scrollTo(0, {$height});");
            sleep($waitSeconds);

            $newHeight = (int) $driver->executeScript('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);');
            if ($newHeight === $height || $newHeight === $lastHeight) {
                break; // reached bottom / no more content
            }

            $lastHeight = $height;
            $scrollCount++;
        }

        return $height;
    }

    /**
     * Scrolls up by a percentage of the page height.
     *
     * @param RemoteWebDriver $driver
     * @param int $percentage - Percentage to scroll up (default 50%)
     */
    private function scrollUpByPercentage(RemoteWebDriver $driver, $percentage = 50)
    {
        $height = (int) $driver->executeScript('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);');
        $scrollAmount = (int) floor($height * $percentage / 100);
        $driver->executeScript("window.scrollBy(0, -{$scrollAmount});");
        sleep(1);
    }

    /**
     * Scrolls down by a percentage of the page height.
     *
     * @param RemoteWebDriver $driver
     * @param int $percentage - Percentage to scroll down (default 50%)
     */
    private function scrollDownByPercentage(RemoteWebDriver $driver, $percentage = 50)
    {
        $height = (int) $driver->executeScript('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);');
        $scrollAmount = (int) floor($height * $percentage / 100);
        $driver->executeScript("window.scrollBy(0, {$scrollAmount});");
        sleep(1);
    }

    /**
     * Scrolls to a specific absolute position from top.
     *
     * @param RemoteWebDriver $driver
     * @param int $pixelsFromTop - Pixels from top to scroll to
     */
    private function scrollToAbsolutePosition(RemoteWebDriver $driver, $pixelsFromTop)
    {
        $driver->executeScript("window.scrollTo(0, {$pixelsFromTop});");
        sleep(1);
    }

    /**
     * Scrolls down by a percentage during loop iteration (used for incremental scrolling).
     * Useful when you need to scroll while collecting elements.
     *
     * @param RemoteWebDriver $driver
     * @param int $currentIndex - Current iteration index
     * @param int $divisor - Divide scroll distance by this (default 3 for 33% increments)
     * @param int $baseScrollDistance - Base scroll distance to increment
     * @return int - New scroll distance
     */
    private function scrollDownIncrementalByPercentage(RemoteWebDriver $driver, $currentIndex, $divisor = 3, $baseScrollDistance = 0)
    {
        if ($baseScrollDistance === 0) {
            $baseScrollDistance = (int) $driver->executeScript('return document.body.scrollHeight;') / 2;
        }

        if ($currentIndex % 3 === 0) {
            $newScrollDistance = (int) ($baseScrollDistance + ($baseScrollDistance / $divisor));
            $driver->executeScript("window.scrollTo(0, {$newScrollDistance});");
            sleep(1);
            return $newScrollDistance;
        }

        return $baseScrollDistance;
    }

    /**
     * Gets the current total scroll height of the page.
     *
     * @param RemoteWebDriver $driver
     * @return int - Total scroll height
     */
    private function getPageTotalHeight(RemoteWebDriver $driver)
    {
        return (int) $driver->executeScript('return Math.max(document.body.scrollHeight, document.documentElement.scrollHeight);');
    }

    /**
     * Gets the current scroll position from top.
     *
     * @param RemoteWebDriver $driver
     * @return int - Current scroll position
     */
    private function getCurrentScrollPosition(RemoteWebDriver $driver)
    {
        return (int) $driver->executeScript('return window.pageYOffset || document.documentElement.scrollTop;');
    }

    //endregion
}
