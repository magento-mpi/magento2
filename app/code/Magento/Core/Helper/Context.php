<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Magento_Core_Helper_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Magento_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Magento_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /** @var  Magento_Core_Model_Config */
    protected $_coreConfig;

    /** @var  Magento_Core_Model_Event_Manager */
    protected $_eventManager;

    /**
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Core_Model_Event_Manager $eventManager
     */
    public function __construct(
        Magento_Core_Model_Translate $translator,
        Magento_Core_Model_ModuleManager $moduleManager,
        Magento_Core_Model_Config $coreConfig,
        Magento_Core_Model_Event_Manager $eventManager
    ) {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
        $this->_coreConfig = $coreConfig;
        $this->_eventManager = $eventManager;
    }

    /**
     * @return Magento_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return Magento_Core_Model_ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }

    /**
     * @return Magento_Core_Model_Config
     */
    public function getCoreConfig()
    {
        return $this->_coreConfig;
    }

    /**
     * @return Magento_Core_Model_Event_Manager
     */
    public function getEventManager()
    {
        return $this->_eventManager;
    }
}
