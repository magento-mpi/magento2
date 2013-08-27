<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Link extends Mage_Page_Block_Link
{
    /**
     * @var Mage_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /**
     * @param Mage_Core_Block_Template_Context $context
     * @param Mage_Core_Model_ModuleManager $moduleManager
     * @param array $data
     */
    public function __construct(
        Mage_Core_Block_Template_Context $context,
        Mage_Core_Model_ModuleManager $moduleManager,
        array $data = array()
    ) {
        parent::__construct($context, $data);
        $this->_moduleManager = $moduleManager;
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout', array('_secure' => true));
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->helper('Mage_Checkout_Helper_Data')->canOnepageCheckout()
            || !$this->_moduleManager->isOutputEnabled('Mage_Checkout')
        ) {
            return '';
        }
        return parent::_toHtml();
    }
}
