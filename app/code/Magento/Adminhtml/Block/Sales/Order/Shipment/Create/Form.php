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
 * Adminhtml shipment create form
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */

class Magento_Adminhtml_Block_Sales_Order_Shipment_Create_Form extends Magento_Adminhtml_Block_Sales_Order_Abstract
{
    /**
     * Retrieve invoice order
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return Magento_Sales_Model_Order_Invoice
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return Magento_Sales_Model_Order_Shipment
     */
    public function getShipment()
    {
        return Mage::registry('current_shipment');
    }

    protected function _prepareLayout()
    {
//        $infoBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_View_Info')
//            ->setOrder($this->getShipment()->getOrder());
//        $this->setChild('order_info', $infoBlock);

        $this->addChild('items', 'Magento_Adminhtml_Block_Sales_Order_Shipment_Create_Items');
        $this->addChild('tracking', 'Magento_Adminhtml_Block_Sales_Order_Shipment_Create_Tracking');
//        $paymentInfoBlock = $this->getLayout()->createBlock('Magento_Adminhtml_Block_Sales_Order_Payment')
//            ->setPayment($this->getShipment()->getOrder()->getPayment());
//        $this->setChild('payment_info', $paymentInfoBlock);

//        return parent::_prepareLayout();
    }

    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getShipment()->getOrderId()));
    }
}
