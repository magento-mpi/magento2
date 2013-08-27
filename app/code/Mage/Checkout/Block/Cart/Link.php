<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Checkout_Block_Cart_Link extends Mage_Page_Block_Link
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
    public function getLabel()
    {
        return $this->_createLabel($this->_getItemCount());
    }

    /**
     * @return string
     */
    public function getHref()
    {
        return $this->getUrl('checkout/cart');
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {
        if ($this->_moduleManager->isOutputEnabled('Mage_Checkout')) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Count items in cart
     *
     * @return int
     */
    protected function _getItemCount()
    {
        $count = $this->getSummaryQty();
        return $count ? $count : $this->helper('Mage_Checkout_Helper_Cart')->getSummaryCount();
    }

    /**
     * Create link label based on cart item quantity
     *
     * @param int $count
     * @return string
     */
    protected function _createLabel($count)
    {
        if ($count == 1) {
            return $this->__('My Cart (%s item)', $count);
        } elseif ($count > 0) {
            return $this->__('My Cart (%s items)', $count);
        } else {
            return $this->__('My Cart');
        }
    }
}
