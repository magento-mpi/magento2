<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

/**
 * Less cache manager interface
 */
interface CacheManagerInterface
{
    /**
     * @return $this
     */
    public function clearCache();

    /**
     * @return null|string
     */
    public function getCachedFile();

    /**
     * @param string $filePath
     * @param array $params
     */
    public function addEntityToCache($filePath, $params);

    /**
     * @param string $generatedFile
     * @return $this
     */
    public function saveCache($generatedFile);
}
