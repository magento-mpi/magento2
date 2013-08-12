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
 * Adminhtml invoice create form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Sales_Order_Invoice_Create_Form extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getInvoice()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getInvoice();
    }

    /**
     * Retrieve invoice model instance
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    public function getInvoice()
    {
        return Mage::registry('current_invoice');
    }

    protected function _prepareLayout()
    {
      /*  $infoBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_View_Info')
           ->setOrder($this->getInvoice()->getOrder());
       $this->setChild('order_info', $infoBlock);
*/
     /*  $this->addChild('items', 'Magento_Adminhtml_Block_Sales_Order_Invoice_Create_Items');
        */
        $trackingBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_Invoice_Create_Tracking');
       //$this->setChild('order_tracking', $trackingBlock);
          $this->setChild('tracking', $trackingBlock);


              /*
        $paymentInfoBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_Payment')
           ->setPayment($this->getInvoice()->getOrder()->getPayment());
        $this->setChild('payment_info', $paymentInfoBlock);
        */
        return parent::_prepareLayout();
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getInvoice()->getOrderId()));
    }

    public function canCreateShipment()
    {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->getQtyToShip()) {
                return true;
            }
        }
        return false;
    }

    public function hasInvoiceShipmentTypeMismatch() {
        foreach ($this->getInvoice()->getAllItems() as $item) {
            if ($item->getOrderItem()->isChildrenCalculated() && !$item->getOrderItem()->isShipSeparately()) {
                return true;
            }
        }
        return false;
    }

    public function canShipPartiallyItem()
    {
        $value = $this->getOrder()->getCanShipPartiallyItem();
        if (!is_null($value) && !$value) {
            return false;
        }
        return true;
    }

    /**
     * Return forced creating of shipment flag
     *
     * @return integer
     */
    public function getForcedShipmentCreate()
    {
        return (int) $this->getOrder()->getForcedShipmentWithInvoice();
    }
}
