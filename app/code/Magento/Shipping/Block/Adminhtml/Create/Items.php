<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Adminhtml shipment items grid
 */
namespace Magento\Shipping\Block\Adminhtml\Create;

class Items extends \Magento\Sales\Block\Adminhtml\Items\AbstractItems
{
    /**
     * Sales data
     *
     * @var \Magento\Sales\Helper\Data
     */
    protected $_salesData = null;

    /**
     * @var \Magento\Shipping\Model\Carrier\Factory
     */
    protected $_carrierFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Catalog\Model\ProductFactory $productFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Sales\Helper\Data $salesData
     * @param \Magento\Shipping\Model\Carrier\Factory $carrierFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Catalog\Model\ProductFactory $productFactory,
        \Magento\Core\Model\Registry $registry,
        \Magento\Sales\Helper\Data $salesData,
        \Magento\Shipping\Model\Carrier\Factory $carrierFactory,
        array $data = array()
    ) {
        $this->_salesData = $salesData;
        $this->_carrierFactory = $carrierFactory;
        parent::__construct($context, $productFactory, $registry, $data);
    }

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

    /**
     * Prepare child blocks
     */
    protected function _beforeToHtml()
    {
        $this->addChild('submit_button', 'Magento\Backend\Block\Widget\Button', array(
            'label'     => __('Submit Shipment'),
            'class'     => 'save submit-button',
            'onclick'   => 'submitShipment(this);',
        ));

        return parent::_beforeToHtml();
    }

    /**
     * Format given price
     *
     * @param float $price
     * @return string
     */
    public function formatPrice($price)
    {
        return $this->getShipment()->getOrder()->formatPrice($price);
    }

    /**
     * Retrieve HTML of update button
     *
     * @return string
     */
    public function getUpdateButtonHtml()
    {
        return $this->getChildHtml('update_button');
    }

    /**
     * Get url for update
     *
     * @return string
     */
    public function getUpdateUrl()
    {
        return $this->getUrl('sales/*/updateQty', array('order_id'=>$this->getShipment()->getOrderId()));
    }

    /**
     * Check possibility to send shipment email
     *
     * @return bool
     */
    public function canSendShipmentEmail()
    {
        return $this->_salesData->canSendNewShipmentEmail($this->getOrder()->getStore()->getId());
    }

    /**
     * Checks the possibility of creating shipping label by current carrier
     *
     * @return bool
     */
    public function canCreateShippingLabel()
    {
        $shippingCarrier = $this->_carrierFactory->getByOrder($this->getOrder());
        return $shippingCarrier && $shippingCarrier->isShippingLabelsAvailable();
    }
}
