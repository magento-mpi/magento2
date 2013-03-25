<?php
/**
 * Saas cache model
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
class Saas_Saas_Model_Cache extends Mage_Core_Model_Cache
{
    /**
     * @var Mage_Core_Model_Event_Manager
     */
    protected $_eventManager;

    /**
     * @param Magento_ObjectManager $objectManager
     * @param Mage_Core_Model_Cache_Frontend_Pool $frontendPool
     * @param Mage_Core_Model_Cache_Types $cacheTypes
     * @param Mage_Core_Model_ConfigInterface $config
     * @param Mage_Core_Model_Dir $dirs
     * @param Mage_Core_Model_Factory_Helper $helperFactory
     * @param Mage_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_ObjectManager $objectManager,
        Mage_Core_Model_Cache_Frontend_Pool $frontendPool,
        Mage_Core_Model_Cache_Types $cacheTypes,
        Mage_Core_Model_ConfigInterface $config,
        Mage_Core_Model_Dir $dirs,
        Mage_Core_Model_Factory_Helper $helperFactory,
        Mage_Core_Model_Event_Manager $eventManager
    )
    {
        $this->_eventManager = $eventManager;
        parent::__construct($objectManager, $frontendPool, $cacheTypes, $config, $dirs, $helperFactory);
    }

    /**
     * Sets task to the queue
     *
     * @param array|string $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    public function invalidateType($typeCode)
    {
        $this->_eventManager->dispatch('refresh_cache');

        return $this->_callOriginInvalidateType($typeCode);
    }

    /**
     * @param $typeCode
     * @return Mage_Core_Model_CacheInterface
     */
    protected function  _callOriginInvalidateType($typeCode)
    {
        return parent::invalidateType($typeCode);
    }
}
