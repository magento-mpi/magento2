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
     * @param array $arguments
     * @param \Magento\Code\Plugin\InvocationChain $invocationChain
     * @return string|null
     */
    public function aroundProcess(array $arguments, \Magento\Code\Plugin\InvocationChain $invocationChain)
    {
        // check if source path already exist
        if (isset($arguments[3])) {
            return $invocationChain->proceed($arguments);
        }

        $this->initializeCacheManager($arguments[0], $arguments[1]);

        $cachedFile = $this->cacheManager->getCachedFile();
        if (null !== $cachedFile) {
            return $cachedFile;
        }

        try {
            $result = $invocationChain->proceed($arguments);
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
     * @param array $arguments
     * @return array
     */
    public function beforeProcessLessInstructions(array $arguments)
    {
        if (null !== $this->cacheManager) {
            list($lessFilePath, $params) = $arguments;
            $this->cacheManager->addEntityToCache($lessFilePath, $params);
        }
        return $arguments;
    }
}
