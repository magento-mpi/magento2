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
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @var Magento_Core_Model_StoreManager
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Event_Manager $eventDispatcher
     * @param Magento_Core_Model_CacheInterface $cacheManager
     * @param Magento_Core_Model_App_State $appState
     * @param Magento_Core_Model_StoreManager $storeManager
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventDispatcher,
        Magento_Core_Model_CacheInterface $cacheManager,
        Magento_Core_Model_App_State $appState,
        Magento_Core_Model_StoreManager $storeManager
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_appState = $appState;
        $this->_storeManager = $storeManager;
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

    /**
     * @return Magento_Core_Model_App_State
     */
    public function getAppState()
    {
        return $this->_appState;
    }

    /**
     * @return Magento_Core_Model_StoreManager
     */
    public function getStoreManager()
    {
        return $this->_storeManager;
    }
}
