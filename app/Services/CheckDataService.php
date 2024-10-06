<?php
namespace App\Services;
use App\Models\Country;
use Carbon\Carbon;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class CheckDataService
{
    private Collection $countries;
    public function __construct()
    {
        $this->countries = Country::all();
        // Load all country names from the database and store them in the $countries array
//        $this->countries = Country::pluck('name')->map(function ($name) {
//            return strtolower($name); // Convert all names to lowercase
//        })->toArray();
    }
    public function checkCountryExist(string $countryName)
    {
        // Convert the input to lowercase
        $countryName = strtolower($countryName);
        $checkCountryExist = $this->countries->where('name', $countryName)->first();
        if (!empty($checkCountryExist)) {
            return true;
        }else{
            return false;
        }
    }
    public function checkCountryExistWithLike(string $countryName)
    {
        $countryName = strtolower($countryName);
        if(strlen($countryName) <= 3){
            return false;
        }
        $checkCountryExist = $this->countries->first(function ($country) use ($countryName) {
            // Use stripos for case-insensitive "like" matching
            return stripos($country->name, $countryName) !== false;
        });
        //$checkCountryExist = $this->countries->where('name', 'like', '%'.$countryName.'%')->first();
        if (!empty($checkCountryExist)) {
            return true;
        }else{
            return false;
        }
    }

}
