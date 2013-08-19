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
 * Adminhtml sales invoices block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Invoice extends Magento_Adminhtml_Block_Widget_Grid_Container
{

    protected function _construct()
    {
        $this->_controller = 'sales_invoice';
        $this->_headerText = __('Invoices');
        parent::_construct();
        $this->_removeButton('add');
    }

    public function getPaymentInfoHtml()
    {
        return $this->getChildHtml('payment_info');
    }
}
