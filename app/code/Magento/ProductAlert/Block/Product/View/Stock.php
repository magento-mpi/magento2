<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Recurring profile view stock
 */
class Magento_ProductAlert_Block_Product_View_Stock extends Magento_ProductAlert_Block_Product_View
{
    /**
     * Prepare stock info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->_helper->isStockAlertAllowed() || !$this->_product || $this->_product->isAvailable()) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_helper->getSaveUrl('stock'));
    }
}
