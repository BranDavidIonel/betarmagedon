<?php
namespace App\Services;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Firefox\FirefoxOptions;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use Facebook\WebDriver\WebDriverWait;
use Laravel\Dusk\Browser;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class ConfigWebDriverService
{
    private const SERVER_SELENIUM_URL = "http://selenium:4444/wd/hub"; // Adress Selenium Server
    public function __construct()
    {

    }

    public function initializeWebDriver()
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
    public function waitForPageReady(RemoteWebDriver $driver, int $waitTimeout = 5){
        $driver->wait($waitTimeout)->until(
            function ($driver) {
                return $driver->executeScript('return document.readyState') === 'complete';
            }
        );
    }
    public  function scrollDownCustom(RemoteWebDriver $driver, $nrScroll = 1 , $height = 700)
    {
        for ($i = 0; $i < $nrScroll; $i++) {
            // Execute JavaScript to scroll down by 700 pixels each time
            $driver->executeScript("window.scrollBy(0, $height);");
        }
    }
}
