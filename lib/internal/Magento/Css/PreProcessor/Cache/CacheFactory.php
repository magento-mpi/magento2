<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

use Magento\Css\PreProcessor\Cache\Import\Cache;

/**
 * Cache manager factory
 */
class CacheFactory
{
    /**
     * @var array
     */
    protected $cacheTypes = [
        Cache::IMPORT_CACHE => 'Magento\Css\PreProcessor\Cache\Import\Cache'
    ];

    /**
     * @var \Magento\ObjectManager
     */
    protected $objectManager;

    /**
     * @param \Magento\ObjectManager $objectManager
     */
    public function __construct(\Magento\ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * @param string $cacheType
     * @param \Magento\View\Publisher\FileInterface $publisherFile
     * @return CacheInterface
     * @throws \InvalidArgumentException
     */
    public function create($cacheType, $publisherFile)
    {
        if (!isset($this->cacheTypes[$cacheType])) {
            throw new \InvalidArgumentException(
                sprintf('No cache type registered for "%s" type.', $cacheType)
            );
        }

        /** @var CacheInterface $cacheManager */
        $cacheManager = $this->objectManager->create(
            $this->cacheTypes[$cacheType], array('publisherFile' => $publisherFile)
        );

        if (!$cacheManager instanceof CacheInterface) {
            throw new \InvalidArgumentException(
                'Cache Manager does not implement \Magento\Css\PreProcessor\Cache\CacheInterface'
            );
        }

        return $cacheManager;
    }
}
