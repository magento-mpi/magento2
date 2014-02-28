<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache\Plugin;

use Magento\Filesystem;
use Magento\Css\PreProcessor\Cache\CacheManager;
use Magento\Css\PreProcessor\Cache\Import\Cache;

/**
 * Plugin for less caching
 */
class Less
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
     * @param \Magento\Css\PreProcessor\Less $subject
     * @param \Closure $proceed
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @param string $targetDirectory
     *
     * @return \Magento\View\Publisher\FileInterface|null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcess(
        \Magento\Css\PreProcessor\Less $subject,
        \Closure $proceed,
        \Magento\View\Publisher\FileInterface $publisherFile,
        $targetDirectory
    ) {
        if ($publisherFile->getSourcePath()) {
            return $proceed($publisherFile, $targetDirectory);
        }

        $this->cacheManager->initializeCacheByType(Cache::IMPORT_CACHE, $publisherFile);

        $cachedFile = $this->cacheManager->getCachedFile(Cache::IMPORT_CACHE);
        if ($cachedFile instanceof \Magento\View\Publisher\FileInterface) {
            return $cachedFile;
        }

        try {
            /** @var \Magento\View\Publisher\FileInterface $result */
            $result = $proceed($publisherFile, $targetDirectory);
            $this->cacheManager->saveCache(Cache::IMPORT_CACHE, $result);
        } catch (Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return null;
        }
        return $result;
    }
}
