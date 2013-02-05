<?php

class Mage_Core_Block_Context
{
    public function __construct(
        Mage_Core_Controller_Request_Http $request,
        Mage_Core_Model_Layout $layout,
        Mage_Core_Model_Event_Manager $eventManager,
        Mage_Core_Model_Url $urlBuilder,
        Mage_Core_Model_Translate $translator,
        Mage_Core_Model_Cache $cache,
        Mage_Core_Model_Design_Package $designPackage,
        Mage_Core_Model_Session_Abstract $session,
        Mage_Core_Model_Store_Config $storeConfig,
        Mage_Core_Controller_Varien_Front $frontController,
        Mage_Core_Model_Factory_Helper $helperFactory,
        array $data = array()
    ) {
        $this->_request         = $request;
        $this->_layout          = $layout;
        $this->_eventManager    = $eventManager;
        $this->_urlBuilder      = $urlBuilder;
        $this->_translator      = $translator;
        $this->_cache           = $cache;
        $this->_designPackage   = $designPackage;
        $this->_session         = $session;
        $this->_storeConfig     = $storeConfig;
        $this->_frontController = $frontController;
        $this->_helperFactory   = $helperFactory;
    }

    public function getCache()
    {
        return $this->_cache;
    }

    public function getDesignPackage()
    {
        return $this->_designPackage;
    }

    public function getEventManager()
    {
        return $this->_eventManager;
    }

    public function getFrontController()
    {
        return $this->_frontController;
    }

    public function getHelperFactory()
    {
        return $this->_helperFactory;
    }

    public function getLayout()
    {
        return $this->_layout;
    }

    public function getRequest()
    {
        return $this->_request;
    }

    public function getSession()
    {
        return $this->_session;
    }

    public function getStoreConfig()
    {
        return $this->_storeConfig;
    }

    public function getTranslator()
    {
        return $this->_translator;
    }

    public function getUrlBuilder()
    {
        return $this->_urlBuilder;
    }
}
