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
class CssPlugin
{
    /**
     * @var CacheManagerInitializer
     */
    protected $initializer;

    /**
     * @var \Magento\Logger
     */
    protected $logger;

    /**
     * @param CacheManagerInitializer $initializer
     * @param \Magento\Logger $logger
     */
    public function __construct(CacheManagerInitializer $initializer, \Magento\Logger $logger)
    {
        $this->initializer = $initializer;
        $this->logger = $logger;
    }

    /**
     * @param \Magento\Css\PreProcessor\Less $subject
     * @param callable $proceed
     * @param string $filePath
     * @param array $params
     * @param string $targetDirectory
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

        $this->initializer->initializeCacheManager($filePath, $params);

        $cachedFile = $this->initializer->getCacheManager()->getCachedFile();
        if (null !== $cachedFile) {
            return $cachedFile;
        }

        try {
            $result = $proceed($filePath, $params, $targetDirectory, $sourcePath);
            $this->initializer->getCacheManager()->saveCache($result);
        } catch (Filesystem\FilesystemException $e) {
            $this->logger->logException($e);
            return null;
        }
        return $result;
    }
}
