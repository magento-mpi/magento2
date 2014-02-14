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
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string|null
     */
    public function aroundProcess(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        /** @var \Magento\View\Publisher\CssFile $publicationFile */
        $publicationFile = $arguments[0];

        if ($publicationFile->getSourcePath()) {
            return $invocationChain->proceed($arguments);
        }

        $this->cacheManager->initializeCacheByType(
            Import\Cache::IMPORT_CACHE,
            $publicationFile
        );

        $cachedFile = $this->cacheManager->getCachedFile(Import\Cache::IMPORT_CACHE);
        if ($cachedFile instanceof \Magento\View\Publisher\FileInterface) {
            return $cachedFile;
        }

        try {
            /** @var \Magento\View\Publisher\FileInterface $result */
            $result = $invocationChain->proceed($arguments);
            $this->cacheManager->saveCache(
                Import\Cache::IMPORT_CACHE,
                $result
            );
        } catch (Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return null;
        }
        return $result;
    }
}
