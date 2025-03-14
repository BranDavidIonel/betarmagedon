<?php
namespace App\Services;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
class DateConversionService
{
    public static function convertDateROtoCarbon(string $dataTextRo)
    {
        $traduceriZile = [
            'luni' => 'Monday', 'Luni' => 'Monday',
            'marți' => 'Tuesday', 'Marți' => 'Tuesday',
            'miercuri' => 'Wednesday', 'Miercuri' => 'Wednesday',
            'joi' => 'Thursday', 'Joi' => 'Thursday',
            'vineri' => 'Friday', 'Vineri' => 'Friday',
            'sâmbătă' => 'Saturday', 'Sâmbătă' => 'Saturday',
            'duminică' => 'Sunday', 'Duminică' => 'Sunday',
        ];

        $traduceriLuni = [
            'ianuarie' => 'January', 'Ianuarie' => 'January',
            'februarie' => 'February', 'Februarie' => 'February',
            'martie' => 'March', 'Martie' => 'March',
            'aprilie' => 'April', 'Aprilie' => 'April',
            'mai' => 'May', 'Mai' => 'May',
            'iunie' => 'June', 'Iunie' => 'June',
            'iulie' => 'July', 'Iulie' => 'July',
            'august' => 'August', 'August' => 'August',
            'septembrie' => 'September', 'Septembrie' => 'September',
            'octombrie' => 'October', 'Octombrie' => 'October',
            'noiembrie' => 'November', 'Noiembrie' => 'November',
            'decembrie' => 'December', 'Decembrie' => 'December'
        ];
        // Translate the day of the week and month
        $convertedDate = strtr($dataTextRo, $traduceriZile);
        $convertedDate = strtr($convertedDate, $traduceriLuni);

        // Check if the year is in two-digit format and convert it to four digits
        $convertedDate = preg_replace('/\b(\d{2})\b/', '20$1', $convertedDate); // Convert '25' to '2025'

        // Now parse the date
        try {
            $dataCarbon = Carbon::createFromFormat('l, d F Y', $convertedDate);
        } catch (\Exception $e) {
            // Log the error and echo for debugging purposes
            echo "Error: " . $e->getMessage();
            echo "Converted Date: " . $convertedDate;
            return null; // Return null or handle as needed
        }

//        $convertedDate = strtr($dataTextRo, $traduceriZile);
//        $convertedDate = strtr($convertedDate, $traduceriLuni);
//        $dataCarbon = Carbon::createFromFormat('l, d F y', $convertedDate);
       return $dataCarbon;
    }
    public static function convertDate_CasaPariurilor(string $dateTime)
    {
        try {
            $dateTime = strtolower(trim($dateTime));
            // Case 1: "today HH:MM"
            if (str_starts_with($dateTime, 'azi ')) {
                $time = trim(substr($dateTime, 4)); // Extract time after "azi "
                // Adjust the time by 2 hours ahead
                $adjustedTime = Carbon::parse($time)->addHours(2);

                return Carbon::today('Europe/Bucharest')->setTimeFromTimeString($adjustedTime->format('H:i'))->format('d-m-Y H:i');
            }
            // Case 2: "tomorrow HH:MM"
            if (str_starts_with($dateTime, 'mâine ')) {
                $time = trim(substr($dateTime, 6)); // Extract time after "mâine "
                Log::error('metoda 2 ->' . $time);
                // Adjust the time by 2 hours ahead
                $adjustedTime = Carbon::parse($time)->addHours(2);

                return Carbon::tomorrow('Europe/Bucharest')->setTimeFromTimeString($adjustedTime->format('H:i'))->format('d-m-Y H:i');
            }
            // Case 3: "day, DD.MM.YYYY, HH:MM"
            $dateArray = explode(',', $dateTime);
            if (count($dateArray) > 2) {
                $datePart = trim($dateArray[1]);
                $timePart = trim($dateArray[2]);
                // Adjust the time by 2 hours ahead
                $adjustedTime = Carbon::parse($timePart)->addHours(2);
                $formattedTime = $adjustedTime->format('H:i');
                return Carbon::createFromFormat('d.m.Y H:i', "$datePart $formattedTime", 'Europe/Bucharest')->format('d-m-Y H:i');
            }

            return null; // If none of the formats match

        } catch (\Exception $e) {
            Log::error('Error in convertDate_CasaPariurilor: ' . $e->getMessage());
            Log::error('Stack trace: ' . $e->getTraceAsString());
            // Optionally return null or a default value
            return null;
        }
    }

}
