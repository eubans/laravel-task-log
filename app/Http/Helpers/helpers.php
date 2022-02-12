<?php

use Carbon\Carbon;

function convertUTCDatetimeToTimezone($datetime, $timezone, $format="Y-m-d H:i:s")
{
    $dateTime = Carbon::createFromFormat("Y-m-d H:i:s", $datetime);
    $dateTime->setTimezone($timezone);
    return $dateTime->format($format);
} 

