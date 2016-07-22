<?php
namespace Library;

use IntlDateFormatter, DateTime;

class Date
{
    public static $locale = 'th_TH@calendar=buddhist';
    public static $timezone = 'Asia/Bangkok';

    public static function format($date, $format = 'วันที่ d / LLLL / y เวลา k.mm น.')
    {
        $formatter = new IntlDateFormatter(
            static::$locale,
            IntlDateFormatter::SHORT,
            IntlDateFormatter::SHORT,
            static::$timezone,
            IntlDateFormatter::TRADITIONAL
        );

        $formatter->setPattern($format);

        if ( ! $date instanceof DateTime) {
            $date = new DateTime($date);
        }

        return $formatter->format($date);
    }
}
