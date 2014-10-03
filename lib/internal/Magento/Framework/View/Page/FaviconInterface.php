<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\View\Page;

/**
 * Favicon interface
 */
interface FaviconInterface
{
    /**
     * @return string
     */
    public function getFaviconFile();

    /**
     * @return string
     */
    public function getDefaultFavicon();
}
