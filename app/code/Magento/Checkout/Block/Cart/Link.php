<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Checkout_Block_Cart_Link extends Magento_Page_Block_Link
{
    /**
     * @var Magento_Core_Model_ModuleManager
     */
    protected $_moduleManager;

    /**
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Core_Block_Template_Context $context
     * @param Magento_Core_Model_ModuleManager $moduleManager
     * @param array $data
     */
    public function __construct(
        Magento_Core_Helper_Data $coreData,
        Magento_Core_Block_Template_Context $context,
        Magento_Core_Model_ModuleManager $moduleManager,
        array $data = array()
    ) {
        parent::__construct($coreData, $context, $data);
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
        if ($this->_moduleManager->isOutputEnabled('Magento_Checkout')) {
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
        return $count ? $count : $this->helper('Magento_Checkout_Helper_Cart')->getSummaryCount();
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
            return __('My Cart (%1 item)', $count);
        } elseif ($count > 0) {
            return __('My Cart (%1 items)', $count);
        } else {
            return __('My Cart');
        }
    }
}
