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

namespace Magento\Rma\Block\Adminhtml\Rma\Create;

class Header extends \Magento\Rma\Block\Adminhtml\Rma\Create\AbstractCreate
{
    protected function _toHtml()
    {
        $customerId = $this->getCustomerId();
        $storeId    = $this->getStoreId();
        $out = '';
        if ($customerId && $storeId) {
            $storeName      = \Mage::app()->getStore($storeId)->getName();
            $customerName   = $this->getCustomerName();
            $out .= __('Create New RMA for %1 in %2', $customerName, $storeName);
        } elseif ($customerId) {
            $out.= __('Create New RMA for %1', $this->getCustomerName());
        } else {
            $out.= __('Create New RMA');
        }
        $out = $this->escapeHtml($out);
        $out = '<h3 class="icon-head head-sales-order">' . $out . '</h3>';
        return $out;
    }
}
