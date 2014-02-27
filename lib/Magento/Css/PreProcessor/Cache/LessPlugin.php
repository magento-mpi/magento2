<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

use Magento\Filesystem;
use Magento\Css\PreProcessor\Cache;

/**
 * Plugin for less caching
 */
class LessPlugin
{
    /**
     * @var CacheManagerInitializer
     */
    protected $cacheManagerProvider;

    /**
     * @param CacheManagerInitializer $cacheManagerProvider
     */
    public function __construct(CacheManagerInitializer $cacheManagerProvider)
    {
        $this->cacheManagerProvider = $cacheManagerProvider;
    }

    /**
     *
     * @param \Magento\Less\PreProcessor $subject
     * @param string $lessFilePath
     * @param array $viewParams
     *
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function beforeProcessLessInstructions(\Magento\Less\PreProcessor $subject, $lessFilePath, $viewParams)
    {
        if (null !== $this->cacheManagerProvider->getCacheManager()) {
            $this->cacheManagerProvider->getCacheManager()->addEntityToCache($lessFilePath, $viewParams);
        }
    }
}
