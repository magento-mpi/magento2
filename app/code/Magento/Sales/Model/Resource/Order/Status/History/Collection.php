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
namespace Magento\Sales\Model\Resource\Order\Status\History;

class Collection
    extends \Magento\Sales\Model\Resource\Order\Collection\AbstractCollection
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
        $this->_init('Magento\Sales\Model\Order\Status\History', 'Magento\Sales\Model\Resource\Order\Status\History');
    }

    /**
     * Get history object collection for specified instance (order, shipment, invoice or credit memo)
     * Parameter instance may be one of the following types: \Magento\Sales\Model\Order,
     * \Magento\Sales\Model\Order\Creditmemo, \Magento\Sales\Model\Order\Invoice, \Magento\Sales\Model\Order\Shipment
     *
     * @param mixed $instance
     * @param string $historyEntityName
     *
     * @return \Magento\Sales\Model\Order\Status\History|null
     */
    public function getUnnotifiedForInstance($instance, $historyEntityName=\Magento\Sales\Model\Order::HISTORY_ENTITY_NAME)
    {
        if(!$instance instanceof \Magento\Sales\Model\Order) {
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
