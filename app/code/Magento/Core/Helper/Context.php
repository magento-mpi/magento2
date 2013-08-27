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

    /**
     * @param Magento_Core_Model_Translate $translator
     * @param Magento_Core_Model_ModuleManager $moduleManager
     */
    public function __construct(Magento_Core_Model_Translate $translator, Magento_Core_Model_ModuleManager $moduleManager)
    {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
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
}
