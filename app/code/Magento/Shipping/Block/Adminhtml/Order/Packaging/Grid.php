<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Shipping\Block\Adminhtml\Order\Packaging;

class Grid extends \Magento\Backend\Block\Template
{

    protected $_template = 'order/packaging/grid.phtml';

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var \Magento\Sales\Model\Order\Shipment\ItemFactory
     */
    protected $_shipmentItemFactory;

    /**
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory
     * @param \Magento\Core\Model\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Sales\Model\Order\Shipment\ItemFactory $shipmentItemFactory,
        \Magento\Core\Model\Registry $registry,
        array $data = array()
    ) {
        $this->_shipmentItemFactory = $shipmentItemFactory;
        $this->_coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        if ($this->getShipment()->getId()) {
            $collection = $this->_shipmentItemFactory->create()->getCollection()
                    ->setShipmentFilter($this->getShipment()->getId());
        } else {
            $collection = $this->getShipment()->getAllItems();
        }
        return $collection;
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
     * Can display customs value
     *
     * @return bool
     */
    public function displayCustomsValue()
    {
        $storeId = $this->getShipment()->getStoreId();
        $order = $this->getShipment()->getOrder();
        $address = $order->getShippingAddress();
        $shipperAddressCountryCode = $this->_storeConfig->getConfig(
            \Magento\Shipping\Model\Shipping::XML_PATH_STORE_COUNTRY_ID,
            $storeId
        );
        $recipientAddressCountryCode = $address->getCountryId();
        if ($shipperAddressCountryCode != $recipientAddressCountryCode) {
            return true;
        }
        return false;
    }

    /**
     * Format price
     *
     * @param   decimal $value
     * @return  double
     */
    public function formatPrice($value)
    {
        return sprintf('%.2F', $value);
    }
}
