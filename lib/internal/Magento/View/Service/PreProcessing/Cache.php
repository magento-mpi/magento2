<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\View\Service\PreProcessing;

class Cache
{
    /**
     * @var \Magento\View\Service\PreProcessing\CacheStorage
     */
    private $cacheStorage;

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
     * @param CacheStorage $cacheStorage
     * @param \Magento\Filesystem\Directory\ReadInterface $sourceDir
     * @param array $directories
     * @throws \InvalidArgumentException
     */
    public function __construct(
        \Magento\View\Service\PreProcessing\CacheStorage $cacheStorage,
        \Magento\Filesystem\Directory\ReadInterface $sourceDir,
        array $directories
    ) {

        $this->cacheStorage = $cacheStorage;
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
     * @param string $sourceFile
     * @return bool|string
     * @throws \UnexpectedValueException
     */
    public function getProcessedFileFromCache($sourceFile)
    {
        $path = false;
        $cacheId = $this->sourceDir->getRelativePath($sourceFile);
        $data = json_decode($this->cacheStorage->load($cacheId), true);
        if ($data) {
            if (!isset($data['path']) || !isset($data['mtime'])) {
                throw new \UnexpectedValueException("Either 'path' or 'mtime' section is not found in cached data");
            }
            $sourceStat = $this->sourceDir->stat($cacheId);
            // Accept cached data only if it's up to date
            if ($sourceStat['mtime'] == $data['mtime']) {
                $path = $this->getAbsolutePath($data['path']);
            }
        }
        return $path;
    }

    /**
     * @param string $processedFile
     * @param string $sourceFile
     * @return bool
     */
    public function saveProcessedFileToCache($processedFile, $sourceFile)
    {
        $cacheId = $this->sourceDir->getRelativePath($sourceFile);
        $cachedPath = $this->getRelativePath($processedFile);
        $sourceStat = $this->sourceDir->stat($cacheId);
        $value = array('path' => $cachedPath, 'mtime' => $sourceStat['mtime']);
        return $this->cacheStorage->save(json_encode($value), $cacheId);
    }

    /**
     * @param string $path
     * @return string
     */
    protected function getAbsolutePath($path)
    {
        $absolutePath = false;
        foreach ($this->directories as $placeholder => $dir) {
            if (substr($path, 0, strlen($placeholder)) == $placeholder) {
                $absolutePath = substr($path, strlen($placeholder) + 1);
                $absolutePath = $dir->getAbsolutePath($absolutePath);
                break;
            }
        }
        return $absolutePath;
    }

    /**
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
}
