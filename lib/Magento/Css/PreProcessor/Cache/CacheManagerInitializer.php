<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

class CacheManagerInitializer 
{
    /**
     * @var \Magento\Css\PreProcessor\Cache\CacheManager
     */
    protected $cacheManager = null;

    /**
     * @var CacheManagerFactory
     */
    protected $cacheManagerFactory;

    /**
     * @param CacheManagerFactory $cacheManagerFactory
     */
    public function __construct(CacheManagerFactory $cacheManagerFactory)
    {
        $this->cacheManagerFactory = $cacheManagerFactory;
    }

    /**
     * Initialize cache manager
     *
     * @param string $lessFilePath
     * @param array $params
     *
     * @return void
     */
    public function initializeCacheManager($lessFilePath, $params)
    {
        $this->cacheManager = $this->cacheManagerFactory->create($lessFilePath, $params);
    }

    /**
     * Get cache manager
     *
     * @return CacheManager|null
     */
    public function getCacheManager()
    {
        return $this->cacheManager;
    }
} 
