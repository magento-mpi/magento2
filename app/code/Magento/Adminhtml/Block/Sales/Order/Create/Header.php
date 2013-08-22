<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Create order form header
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Create_Header extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected function _toHtml()
    {
        if ($this->_getSession()->getOrder()->getId()) {
            return __('Edit Order #%1', $this->_getSession()->getOrder()->getIncrementId());
        }

        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $out.= __('Create New Order for %1 in %2', $this->getCustomer()->getName(), $this->getStore()->getName());
        }
        elseif (!is_null($customerId) && $storeId){
            $out.= __('Create New Order for New Customer in %1', $this->getStore()->getName());
        }
        elseif ($customerId) {
            $out.= __('Create New Order for %1', $this->getCustomer()->getName());
        }
        elseif (!is_null($customerId)){
            $out.= __('Create New Order for New Customer');
        }
        else {
            $out.= __('Create New Order');
        }
        $out = $this->escapeHtml($out);
        return $out;
    }
}
