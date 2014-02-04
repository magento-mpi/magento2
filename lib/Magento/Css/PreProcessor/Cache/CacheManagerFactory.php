<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Css\PreProcessor\Cache;

/**
 * Cache manager factory
 */
class CacheManagerFactory
{
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
     * @param string $filePath
     * @param array $params
     * @return \Magento\Css\PreProcessor\Cache\CacheManager
     * @throws \Exception
     */
    public function create($filePath, array $params)
    {
        /** @var \Magento\Css\PreProcessor\Cache\CacheManagerInterface $cacheManager */
        $cacheManager = $this->objectManager->create(
            'Magento\Css\PreProcessor\Cache\CacheManager',
            array('filePath' => $filePath, 'params' => $params)
        );

        if (!$cacheManager instanceof \Magento\Css\PreProcessor\Cache\CacheManagerInterface) {
            throw new \InvalidArgumentException(
                'Cache Manager does not implement \Magento\Css\PreProcessor\Cache\CacheManagerInterface'
            );
        }

        return $cacheManager;
    }
}
