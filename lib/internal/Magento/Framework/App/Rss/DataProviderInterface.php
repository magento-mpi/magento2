<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Framework\App\Rss;

/**
 * Interface DataProviderInterface
 * @package Magento\Framework\App\Rss
 */
interface DataProviderInterface
{
    /**
     * Check if RSS feed allowed
     *
     * @return mixed
     */
    public function isAllowed();

    /**
     * Get RSS feed items
     *
     * @return array
     */
    public function getRssData();

    /**
     * @return string
     */
    public function getCacheKey();

    /**
     * @return int
     */
    public function getCacheLifetime();

    /**
     * @return array
     */
    public function getFeeds();
}
