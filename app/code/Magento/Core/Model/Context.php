<?php
/**
 * Abstract model context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Model_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventDispatcher;

    /**
     * @var Magento_Core_Model_CacheInterface
     */
    protected $_cacheManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventDispatcher
     * @param Magento_Core_Model_CacheInterface $cacheManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventDispatcher,
        Magento_Core_Model_CacheInterface $cacheManager
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
    }

    /**
     * @return \Magento_Core_Model_CacheInterface
     */
    public function getCacheManager()
    {
        return $this->_cacheManager;
    }

    /**
     * @return \Magento_Core_Model_Event_Manager
     */
    public function getEventDispatcher()
    {
        return $this->_eventDispatcher;
    }
}
