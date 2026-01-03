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
            sleep(2);//for debug I saw in Reimmina the click ;)
            $script = <<<JS
                        try {
                            function queryShadow(selector, root = document) {
                                const all = root.querySelectorAll('*');
                                for (const el of all) {
                                    if (el.shadowRoot) {
                                        const found = queryShadow(selector, el.shadowRoot);
                                        if (found) return found;
                                    }
                                    if (el.matches(selector)) return el;
                                }
                                return null;
                            }

                            const btn = queryShadow('button.uc-accept-button');
                            if (!btn) return 'Error: button not found in shadow DOM';
                            btn.click();
                            return 'Clicked ACCEPT TOATE';
                        } catch(e) {
                            return 'Error: ' + e.message;
                        }
                        JS;

            $result = $driver->executeScript($script);
            //echo $result;
            sleep(2); 
        } catch (\Exception $e) {
            //echo "A apărut o eroare nu gaseste cookie-consent-button-accept-necessary: " . $e->getMessage();
            Log::error("A apărut o eroare nu gaseste cookie-consent-button-accept-necessary: " . $e->getMessage());
        }
    }
}
