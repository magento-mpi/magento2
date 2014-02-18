<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Less\Cache;

use Magento\Css\PreProcessor\Cache\CacheManager;
use Magento\Filesystem;

/**
 * Import instructions caching
 */
class Plugin
{
    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var CacheManager
     */
    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     * @param \Magento\Logger $logger
     */
    public function __construct(
        CacheManager $cacheManager,
        \Magento\Logger $logger
    ) {
        $this->cacheManager = $cacheManager;
        $this->logger = $logger;
    }

    /**
     * @param array $arguments
     * @return array
     */
    public function beforeProcessLessInstructions(array $arguments)
    {
        $this->cacheManager->addToCache(
            \Magento\Css\PreProcessor\Cache\Import\Cache::IMPORT_CACHE,
            $arguments
        );
        return $arguments;
    }
}
