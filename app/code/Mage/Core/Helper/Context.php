<?php
/**
 * Abstract helper context
 *
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */
class Mage_Core_Helper_Context implements Magento_ObjectManager_ContextInterface
{
    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @var Mage_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /**
     * @param Mage_Core_Model_Translate $translator
     * @param Mage_Core_Model_ModuleManager $moduleManager
     */
    public function __construct(Mage_Core_Model_Translate $translator, Mage_Core_Model_ModuleManager $moduleManager)
    {
        $this->_translator = $translator;
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }

    /**
     * @return Mage_Core_Model_ModuleManager
     */
    public function getModuleManager()
    {
        return $this->_moduleManager;
    }
}
