<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml invoice create
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Invoice_Create extends Mage_Adminhtml_Block_Widget_Form_Container
{
    protected function _construct()
    {
        $this->_objectId = 'order_id';
        $this->_controller = 'sales_order_invoice';
        $this->_mode = 'create';

        parent::_construct();

        $this->_removeButton('save');
        $this->_removeButton('delete');
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Mage_Sales_Model_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    /**
     * Retrieve text for header
     *
     * @return string
     */
    public function getHeaderText()
    {
        return ($this->getInvoice()->getOrder()->getForcedShipmentWithInvoice())
            ? Mage::helper('Mage_Sales_Helper_Data')->__('New Invoice and Shipment for Order #%s', $this->getInvoice()->getOrder()->getRealOrderId())
            : Mage::helper('Mage_Sales_Helper_Data')->__('New Invoice for Order #%s', $this->getInvoice()->getOrder()->getRealOrderId());
    }

    /**
     * Retrieve back url
     *
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl(
            '*/sales_order/view',
            array('order_id' => $this->getInvoice() ? $this->getInvoice()->getOrderId() : null)
        );
    }
}
