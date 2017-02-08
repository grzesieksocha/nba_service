<?php declare(strict_types = 1);

namespace AppBundle\WebScrapper\Interfaces;

/**
 * Interface HtmlGetter
 * @package AppBundle\WebScrapper\Interfaces
 */
interface HtmlGetter
{
    /**
     * @param string $path
     * @return string
     */
    public function getHtmlFromPath(string $path);
}