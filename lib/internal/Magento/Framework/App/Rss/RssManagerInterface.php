<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Rss;

/**
 * Interface RssManagerInterface
 * @package Magento\Framework\App\Rss
 */
interface RssManagerInterface
{
    /**
     * @param string $type
     * @return DataProviderInterface
     */
    public function getProvider($type);
}
