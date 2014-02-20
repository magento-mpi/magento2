<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

class CacheManager
{
    /**
     * @var CacheFactory
     */
    protected $cacheFactory;

    /**
     * @var CacheInterface[]
     */
    protected $cacheByType = [];

    /**
     * @param CacheFactory $cacheFactory
     */
    public function __construct(
        CacheFactory $cacheFactory
    ) {
        $this->cacheFactory = $cacheFactory;
    }

    /**
     * @param string $cacheType
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @return $this
     */
    public function initializeCacheByType($cacheType, $publisherFile)
    {
        $this->cacheByType[$cacheType] = $this->cacheFactory->create($cacheType, $publisherFile);
        return $this;
    }

    /**
     * @param string $cacheType
     * @return string|null
     */
    public function getCachedFile($cacheType)
    {
        return $this->isCacheInitialized($cacheType) ? $this->cacheByType[$cacheType]->get() : null;
    }

    /**
     * @param string $cacheType
     * @param \Magento\Less\PreProcessor\File\Less $lessFile
     * @return $this
     */
    public function addToCache($cacheType, $lessFile)
    {
        !$this->isCacheInitialized($cacheType) ?: $this->cacheByType[$cacheType]->add($lessFile);
        return $this;
    }

    /**
     * @param string $cacheType
     * @param string $cacheFile
     * @return $this
     */
    public function saveCache($cacheType, $cacheFile)
    {
        !$this->isCacheInitialized($cacheType) ?: $this->cacheByType[$cacheType]->save($cacheFile);
        return $this;
    }

    /**
     * @param string $cacheType
     * @return $this
     */
    public function clearCache($cacheType)
    {
        !$this->isCacheInitialized($cacheType) ?: $this->cacheByType[$cacheType]->clear();
        return $this;
    }

    /**
     * @param string $cacheType
     * @return bool
     */
    public function isCacheInitialized($cacheType)
    {
        return isset($this->cacheByType[$cacheType]);
    }
}
