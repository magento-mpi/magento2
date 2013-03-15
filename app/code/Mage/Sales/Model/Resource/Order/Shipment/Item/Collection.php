<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Flat sales order shipment items collection
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Resource_Order_Shipment_Item_Collection extends Mage_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_item_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_shipment_item_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Mage_Sales_Model_Order_Shipment_Item', 'Mage_Sales_Model_Resource_Order_Shipment_Item');
    }

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return Mage_Sales_Model_Resource_Order_Shipment_Item_Collection
     */
    public function setShipmentFilter($shipmentId)
    {
        $this->addFieldToFilter('parent_id', $shipmentId);
        return $this;
    }
}
