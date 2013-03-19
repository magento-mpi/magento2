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
     * @param Mage_Core_Model_Translate $translator
     */
    public function __construct(Mage_Core_Model_Translate $translator)
    {
        $this->_translator = $translator;
    }

    /**
     * @return \Mage_Core_Model_Translate
     */
    public function getTranslator()
    {
        return $this->_translator;
    }
}
