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
 * Flat sales order status history resource
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Status_History extends Magento_Sales_Model_Resource_Order_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_status_history_resource';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('sales_flat_order_status_history', 'entity_id');
    }
}
