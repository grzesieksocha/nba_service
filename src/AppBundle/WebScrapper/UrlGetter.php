<?php declare(strict_types = 1);

namespace AppBundle\WebScrapper;

use AppBundle\WebScrapper\Interfaces\HtmlGetter;

/**
 * Class HTMLGetter
 * @package AppBundle\WebScrapper
 */
class UrlGetter implements HtmlGetter
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function getHtmlFromPath(string $path)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $path);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $html = curl_exec($curl);
        curl_close($curl);

        return $html;
    }
}