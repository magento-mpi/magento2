<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Asset\File\Source;

class Cache
{
    /**
     * @var \Magento\View\Asset\File\Source\CacheType
     */
    private $cache;

    /**
     * @var \Magento\Filesystem\Directory\ReadInterface
     */
    private $sourceDir;

    /**
     * Placeholder-directory map
     *
     * @var \Magento\Filesystem\Directory\ReadInterface[]
     */
    private $directories = [];

    /**
     * @param \Magento\View\Asset\File\Source\CacheType $cache
     * @param \Magento\Filesystem\Directory\ReadInterface $sourceDir
     * @param array $directories
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\View\Asset\File\Source\CacheType $cache,
        \Magento\Filesystem\Directory\ReadInterface $sourceDir,
        array $directories
    ) {

        $this->cache = $cache;
        $this->sourceDir = $sourceDir;
        foreach ($directories as $dir) {
            if (!($dir instanceof \Magento\Filesystem\Directory\ReadInterface)) {
                throw new \InvalidArgumentException(
                    '$directories must be a list of \Magento\Filesystem\Directory\ReadInterface'
                );
            }
        }
        $this->directories = $directories;
    }

    /**
     * Retrieve data from cache with replacement of relative paths back to absolute ones
     *
     * @param string $sourceFile
     * @param \Magento\View\Asset\LocalInterface $asset
     * @return bool|string
     */
    public function getProcessedFileFromCache($sourceFile, \Magento\View\Asset\LocalInterface $asset)
    {
        $path = false;
        $cacheId = $this->getCacheId($sourceFile, $asset);
        $relativePath = $this->cache->load($cacheId);
        if ($relativePath) {
            $path = $this->getAbsolutePath($relativePath);
        }
        return $path;
    }

    /**
     * Save data to cache with replacement of absolute paths to relative ones
     *
     * @param string $processedFile
     * @param string $sourceFile
     * @param \Magento\View\Asset\LocalInterface $asset
     * @return bool
     */
    public function saveProcessedFileToCache($processedFile, $sourceFile, \Magento\View\Asset\LocalInterface $asset)
    {
        $cacheId = $this->getCacheId($sourceFile, $asset);
        $relativePath = $this->getRelativePath($processedFile);
        return $this->cache->save($relativePath, $cacheId);
    }

    /**
     * Get absolute path basing on placeholder for a directory in the relative path
     *
     * Example: %root%/somewhere/file.ext -> /root/somewhere/file.ext (if %root% is configured to /root)
     *
     * @param string $path
     * @return string
     */
    protected function getAbsolutePath($path)
    {
        $absolutePath = false;
        foreach ($this->directories as $placeholder => $dir) {
            if (substr($path, 0, strlen($placeholder)) == $placeholder) {
                $relativePath = substr($path, strlen($placeholder) + 1);
                if ($dir->isExist($relativePath)) {
                    $absolutePath = $dir->getAbsolutePath($relativePath);
                    break;
                }
            }
        }
        return $absolutePath;
    }

    /**
     * Get relative path for the absolute path, replacing base directory to one of configured placeholders
     *
     * Example: /root/somewhere/file.ext -> %root%/somewhere/file.ext (if %root% is configured to /root)
     *
     * @param string $path
     * @return bool|string
     */
    protected function getRelativePath($path)
    {
        $cachedPath = false;
        foreach ($this->directories as $placeholder => $dir) {
            $dirPath = $dir->getAbsolutePath();
            if (substr($path, 0, strlen($dirPath)) == $dirPath) {
                $cachedPath = $dir->getRelativePath($path);
                $cachedPath = $placeholder . '/' . $cachedPath;
                break;
            }
        }
        return $cachedPath;
    }

    /**
     * @param string $sourceFile
     * @param \Magento\View\Asset\LocalInterface $asset
     * @return string
     */
    protected function getCacheId($sourceFile, \Magento\View\Asset\LocalInterface $asset)
    {
        return $this->sourceDir->getRelativePath($sourceFile) . ':' . $asset->getRelativePath();
    }
}
