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
 * Adminhtml sales order create shipping method block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Create_Shipping_Method extends Magento_Adminhtml_Block_Sales_Order_Create_Abstract
{

    protected function _construct()
    {
        parent::_construct();
        $this->setId('sales_order_create_shipping_method');
    }

    public function getHeaderText()
    {
        return __('Shipping Method');
    }

    public function getHeaderCssClass()
    {
        return 'head-shipping-method';
    }
}
