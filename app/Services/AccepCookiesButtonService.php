<?php
namespace App\Services;
use Carbon\Carbon;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;

class AccepCookiesButtonService
{
    /**
     * Check if the cookie consent button is present and click it if found.
     *
     * @param RemoteWebDriver $driver The WebDriver instance.
     */
    public static function acceptCookiesCasaPariurilor(RemoteWebDriver $driver) {
        try {
            sleep(1);//for debug I saw in Reimmina the click ;)
            // Try to find the cookie consent button
            $acceptButton = $driver->findElement(WebDriverBy::id('cookie-consent-button-accept-necessary'));
            if ($acceptButton) {
                $acceptButton->click();
                sleep(1);
            }
        } catch (\Exception $e) {
            //echo "A apărut o eroare nu gaseste cookie-consent-button-accept-necessary: " . $e->getMessage();
            Log::error("A apărut o eroare nu gaseste cookie-consent-button-accept-necessary: " . $e->getMessage());
        }
    }
}
