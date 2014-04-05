<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Design\FileResolution\Fallback;

interface CacheDataInterface
{
    /**
     * Retrieve cached value by file name and parameters
     *
     * @param string $type
     * @param string $file
     * @param array $params
     * @return string
     */
    public function getFromCache($type, $file, array $params);

    /**
     * Save value to cache as unique to file name and parameters
     *
     * @param string $value
     * @param string $type
     * @param string $file
     * @param array $params
     * @return bool
     */
    public function saveToCache($value, $type, $file, array $params);
}
