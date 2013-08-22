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
 * Flat sales order shipment comments collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Shipment_Comment_Collection
    extends Magento_Sales_Model_Resource_Order_Comment_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_comment_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_shipment_comment_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Shipment_Comment', 'Magento_Sales_Model_Resource_Order_Shipment_Comment');
    }

    /**
     * Set shipment filter
     *
     * @param int $shipmentId
     * @return Magento_Sales_Model_Resource_Order_Shipment_Comment_Collection
     */
    public function setShipmentFilter($shipmentId)
    {
        return $this->setParentFilter($shipmentId);
    }
}
