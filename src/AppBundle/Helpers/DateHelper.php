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
        $startOfDay->setTime(0, 0);
        $endOfDay = clone $startOfDay;
        $endOfDay->setTime(23, 59, 59);

        $startOfDay->setTimezone($timezone);
        $endOfDay->setTimezone($timezone);

        return [$startOfDay, $endOfDay];
    }
}