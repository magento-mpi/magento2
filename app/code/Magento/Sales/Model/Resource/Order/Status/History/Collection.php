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
 * Flat sales order status history collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Resource_Order_Status_History_Collection
    extends Magento_Sales_Model_Resource_Order_Collection_Abstract
{
    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix    = 'sales_order_status_history_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject    = 'order_status_history_collection';

    /**
     * Model initialization
     *
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Order_Status_History', 'Magento_Sales_Model_Resource_Order_Status_History');
    }

    /**
     * Get history object collection for specified instance (order, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: Magento_Sales_Model_Order,
     * Magento_Sales_Model_Order_Creditmemo, Magento_Sales_Model_Order_Invoice, Magento_Sales_Model_Order_Shipment
     *
     * @param mixed $instance
     * @param string $historyEntityName
     *
     * @return Magento_Sales_Model_Order_Status_History|null
     */
    public function getUnnotifiedForInstance($instance, $historyEntityName=Magento_Sales_Model_Order::HISTORY_ENTITY_NAME)
    {
        if(!$instance instanceof Magento_Sales_Model_Order) {
            $instance = $instance->getOrder();
        }
        $this->setOrderFilter($instance)->setOrder('created_at', 'desc')
            ->addFieldToFilter('entity_name', $historyEntityName)
            ->addFieldToFilter('is_customer_notified', 0)->setPageSize(1);
        foreach($this as $historyItem) {
            return $historyItem;
        }
        return null;
    }

}
