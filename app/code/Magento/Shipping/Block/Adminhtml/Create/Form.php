<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Shipping\Block\Adminhtml\Create;

/**
 * Adminhtml shipment create form
 */
class Form extends \Magento\Sales\Block\Adminhtml\Order\AbstractOrder
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
     * @return \Magento\Sales\Model\Order\Shipment
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

    /**
     * @return \Magento\View\Element\AbstractBlock
     */
    protected function _prepareLayout()
    {
        $this->addChild('items', 'Magento\Shipping\Block\Adminhtml\Create\Items');
        return parent::_prepareLayout();
    }

    /**
     * @return string
     */
    public function getPaymentHtml()
    {
        return $this->getChildHtml('order_payment');
    }

    /**
     * @return string
     */
    public function getItemsHtml()
    {
        return $this->getChildHtml('order_items');
    }

    /**
     * @return string
     */
    public function getSaveUrl()
    {
        return $this->getUrl('*/*/save', array('order_id' => $this->getShipment()->getOrderId()));
    }
}
