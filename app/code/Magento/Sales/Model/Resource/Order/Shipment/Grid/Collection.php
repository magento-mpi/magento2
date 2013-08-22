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
 * Flat sales order shipment collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Shipment_Grid_Collection
    extends Magento_Sales_Model_Resource_Order_Shipment_Collection
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_shipment_grid_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_shipment_grid_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setMainTable('sales_flat_shipment_grid');
    }
}
