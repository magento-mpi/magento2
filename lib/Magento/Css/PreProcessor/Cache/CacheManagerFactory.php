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
     */
    public function create($filePath, array $params)
    {
        return $this->objectManager->create(
            'Magento\Css\PreProcessor\Cache\CacheManager',
            array('filePath' => $filePath, 'params' => $params)
        );
    }
}
