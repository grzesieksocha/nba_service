<?php declare(strict_types = 1);

namespace AppBundle\WebScrapper;

use AppBundle\WebScrapper\Interfaces\HtmlGetter;

/**
 * Class HTMLGetter
 * @package AppBundle\WebScrapper
 */
class FileGetter implements HtmlGetter
{
    /**
     * @param string $path
     *
     * @return string
     */
    public function getHtmlFromPath(string $path)
    {
        return file_get_contents($path);
    }
}