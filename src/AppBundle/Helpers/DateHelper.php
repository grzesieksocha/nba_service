<?php declare(strict_types = 1);

namespace AppBundle\Helpers;

use \DateTime;
use \DateTimeZone;

/**
 * Class DateHelper
 * @package AppBundle\Helpers
 */
class DateHelper
{
    /**
     * @param DateTime $startOfDay
     *
     * @return DateTime[]
     */
    public static function getTodayBordersFromEstToCet(DateTime $startOfDay) {
        $timezone = new DateTimeZone('CET');
        list($startOfDay, $endOfDay) = self::getTodayBorders($startOfDay);

        $startOfDay->setTimezone($timezone);
        $endOfDay->setTimezone($timezone);

        return [$startOfDay, $endOfDay];
    }

    /**
     * @param DateTime $startOfDay
     *
     * @return DateTime[]
     */
    public static function getTodayBorders(DateTime $startOfDay)
    {
        $startOfDay->setTime(0, 0);
        $endOfDay = clone $startOfDay;
        $endOfDay->setTime(23, 59, 59);

        return [$startOfDay, $endOfDay];
    }

    /**
     * Method converts sting date from format YYYY/MM/DD into given timezone DateTime
     *
     * @param string $date
     * @param string $timezone
     * @return DateTime
     */
    public static function getEstDateTimeFromString(string $date, string $timezone = 'UTC')
    {
        $date = explode('/', $date);
        $timezone = new DateTimeZone($timezone);
        $dateTime = new DateTime('now', $timezone);
        $dateTime->setDate((int)$date[0], (int)$date[1], (int)$date[2]);
        $dateTime->setTime(0, 0);
        return $dateTime;
    }
}