<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Admin RMA create form header
 *
 * @category    Magento
 * @package     Magento_Rma
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Rma_Block_Adminhtml_Rma_Create_Header extends Magento_Rma_Block_Adminhtml_Rma_Create_Abstract
{
    protected function _toHtml()
    {
        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $storeName      = Mage::app()->getStore($storeId)->getName();
            $customerName   = $this->getCustomerName();
            $out .= Mage::helper('Magento_Rma_Helper_Data')->__('Create New RMA for %s in %s', $customerName, $storeName);
        } elseif ($customerId) {
            $out.= Mage::helper('Magento_Rma_Helper_Data')->__('Create New RMA for %s', $this->getCustomerName());
        } else {
            $out.= Mage::helper('Magento_Rma_Helper_Data')->__('Create New RMA');
        }
        $out = $this->escapeHtml($out);
        $out = '<h3 class="icon-head head-sales-order">' . $out . '</h3>';
        return $out;
    }
}
