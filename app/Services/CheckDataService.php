<?php
namespace App\Services;
use App\Models\Country;
use Carbon\Carbon;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Facades\Log;

class CheckDataService
{
    private array $countries = [];
    public function __construct()
    {
        // Load all country names from the database and store them in the $countries array
        $this->countries = Country::pluck('name')->map(function ($name) {
            return strtolower($name); // Convert all names to lowercase
        })->toArray();
    }
    public function checkCountryExist($countryName)
    {
        // Convert the input to lowercase
        $countryName = strtolower($countryName);
        // Check if the country exists in the array
        return in_array($countryName, $this->countries);
    }

}
