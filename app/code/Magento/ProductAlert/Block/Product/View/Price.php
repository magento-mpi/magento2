<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ProductAlert
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Product view price
 */
class Magento_ProductAlert_Block_Product_View_Price extends Magento_ProductAlert_Block_Product_View
{
    /**
     * Prepare price info
     *
     * @return void
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        if (!$this->_helper->isPriceAlertAllowed()
            || !$this->_product || false === $this->_product->getCanShowPrice()
        ) {
            $this->setTemplate('');
            return;
        }
        $this->setSignupUrl($this->_helper->getSaveUrl('price'));
    }

}
