<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Grid of packaging shipment
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Adminhtml\Block\Sales\Order\Shipment\Packaging;

class Grid extends \Magento\Adminhtml\Block\Template
{

    protected $_template = 'sales/order/shipment/packaging/grid.phtml';

    /**
     * Return collection of shipment items
     *
     * @return array
     */
    public function getCollection()
    {
        if ($this->getShipment()->getId()) {
            $collection = \Mage::getModel('Magento\Sales\Model\Order\Shipment\Item')->getCollection()
                    ->setShipmentFilter($this->getShipment()->getId());
        } else{
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
        return \Mage::registry('current_shipment');
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
        $shipperAddressCountryCode = \Mage::getStoreConfig(
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
