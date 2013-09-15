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
 */

namespace Magento\Adminhtml\Block\Sales\Order\Shipment\Create;

class Form extends \Magento\Adminhtml\Block\Sales\Order\AbstractOrder
{
    /**
     * Retrieve invoice order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->getShipment()->getOrder();
    }

    /**
     * Retrieve source
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function getSource()
    {
        return $this->getShipment();
    }

    /**
     * Retrieve shipment model instance
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function getShipment()
    {
        return $this->_coreRegistry->registry('current_shipment');
    }

    protected function _prepareLayout()
    {
        $this->addChild('items', 'Magento\Adminhtml\Block\Sales\Order\Shipment\Create\Items');
        $this->addChild('tracking', 'Magento\Adminhtml\Block\Sales\Order\Shipment\Create\Tracking');
        return parent::_prepareLayout();
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
