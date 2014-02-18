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
     * @var CacheManagerFactory
     */
    protected $cacheManagerFactory;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @var \Magento\Css\PreProcessor\Cache\CacheManager
     */
    protected $cacheManager;

    /**
     * @param CacheManagerFactory $cacheManagerFactory
     * @param \Magento\Logger $logger
     */
    public function __construct(
        CacheManagerFactory $cacheManagerFactory,
        \Magento\Logger $logger
    ) {
        $this->cacheManagerFactory = $cacheManagerFactory;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Css\PreProcessor\Less $subject
     * @param callable $proceed
     * @param $filePath
     * @param $params
     * @param $targetDirectory
     * @param null $sourcePath
     *
     * @return null|string
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundProcess(
        \Magento\Css\PreProcessor\Less $subject,
        \Closure $proceed,
        $filePath,
        $params,
        $targetDirectory,
        $sourcePath = null
    ) {
        // check if source path already exist
        if (isset($sourcePath)) {
            return $proceed($filePath, $params, $targetDirectory, $sourcePath);
        }

        $this->initializeCacheManager($filePath, $params);

        $cachedFile = $this->cacheManager->getCachedFile();
        if (null !== $cachedFile) {
            return $cachedFile;
        }

        try {
            $result = $proceed($filePath, $params, $targetDirectory, $sourcePath);
            $this->cacheManager->saveCache($result);
        } catch (Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return null;
        }
        return $result;
    }

    /**
     * @param string $lessFilePath
     * @param array $params
     * @return $this
     */
    protected function initializeCacheManager($lessFilePath, $params)
    {
        $this->cacheManager = $this->cacheManagerFactory->create($lessFilePath, $params);
        return $this;
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
        if (null !== $this->cacheManager) {
            $this->cacheManager->addEntityToCache($lessFilePath, $viewParams);
        }
    }
}
