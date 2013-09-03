<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Rma_Block_Order_Link extends Magento_Sales_Block_Order_Link
{
    /**
     * @inheritdoc
     * @return string
     */
    protected function _toHtml()
    {
        if (!$this->_isRmaAviable()) {
            return '';
        }
        return parent::_toHtml();
    }

    /**
     * Get is link aviable
     * @return bool
     */
    protected function _isRmaAviable()
    {
        if (Mage::helper('Magento_Rma_Helper_Data')->isEnabled()) {
            $returns = Mage::getResourceModel('Magento_Rma_Model_Resource_Rma_Grid_Collection')
                ->addFieldToSelect('*')
                ->addFieldToFilter('order_id', Mage::registry('current_order')->getId())
                ->count();

            return $returns > 0;
        } else {
            return false;
        }
    }
}
