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
     * @var Magento_Core_Model_Logger
     */
    protected $_logger;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @var Magento_Core_Model_App_State
     */
    protected $_appState;

    /**
     * @param Magento_Core_Model_Logger $logger
     * @param Magento_Core_Model_Event_Manager $eventDispatcher
     * @param Magento_Core_Model_CacheInterface $cacheManager
     * @param Magento_Core_Model_App_State $appState
     */
    public function __construct(
        Magento_Core_Model_Logger $logger,
        Magento_Core_Model_Event_Manager $eventDispatcher,
        Magento_Core_Model_CacheInterface $cacheManager,
        Magento_Core_Model_App_State $appState
    ) {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_cacheManager = $cacheManager;
        $this->_appState = $appState;
        $this->_logger = $logger;
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
     * @return Magento_Core_Model_Logger
     */
    public function getLogger()
    {
        return $this->_logger;
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
