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
 * Adminhtml sales order create payment method block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Billing_Method extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{
    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_billing_method');
    }

    public function getHeaderText()
    {
        return __('Payment Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-payment-method';
    }
}
