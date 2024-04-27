<?php
//app/Services/DateConversionService.php

namespace App\Services;

use Carbon\Carbon;

class DateConversionService
{
    public static function convertDateROtoCarbon(string $dataTextRo)
    {
        $traduceriZile = [
            'luni' => 'Monday',
            'marți' => 'Tuesday',
            'miercuri' => 'Wednesday',
            'joi' => 'Thursday',
            'vineri' => 'Friday',
            'sâmbătă' => 'Saturday',
            'duminică' => 'Sunday',
        ];

        $traduceriLuni = [
            'ianuarie' => 'January',
            'februarie' => 'February',
            'martie' => 'March',
            'aprilie' => 'April',
            'mai' => 'May',
            'iunie' => 'June',
            'iulie' => 'July',
            'august' => 'August',
            'septembrie' => 'September',
            'octombrie' => 'October',
            'noiembrie' => 'November',
            'decembrie' => 'December',
        ];

        $convertedDate = strtr($dataTextRo, $traduceriZile);
        $convertedDate = strtr($convertedDate, $traduceriLuni);
        $dataCarbon = Carbon::createFromFormat('l, d F y', $convertedDate);
        return $dataCarbon;
    }
}
