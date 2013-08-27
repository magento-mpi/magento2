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
 * Flat sales order collection
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
abstract class Magento_Sales_Model_Resource_Order_Collection_Abstract extends Magento_Sales_Model_Resource_Collection_Abstract
{
    /**
     * Order object
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_salesOrder   = null;

    /**
     * Order field for setOrderFilter
     *
     * @var string
     */
    protected $_orderField   = 'parent_id';

    /**
     * Set sales order model as parent collection object
     *
     * @param Magento_Sales_Model_Order $order
     * @return Magento_Sales_Model_Resource_Order_Collection_Abstract
     */
    public function setSalesOrder($order)
    {
        $this->_salesOrder = $order;
        if ($this->_eventPrefix && $this->_eventObject) {
            Mage::dispatchEvent($this->_eventPrefix . '_set_sales_order', array(
                'collection' => $this,
                $this->_eventObject => $this,
                'order' => $order
            ));
        }

        return $this;
    }

    /**
     * Retrieve sales order as parent collection object
     *
     * @return Magento_Sales_Model_Order|null
     */
    public function getSalesOrder()
    {
        return $this->_salesOrder;
    }

    /**
     * Add order filter
     *
     * @param int|Magento_Sales_Model_Order $order
     * @return Magento_Sales_Model_Resource_Order_Collection_Abstract
     */
    public function setOrderFilter($order)
    {
        if ($order instanceof Magento_Sales_Model_Order) {
            $this->setSalesOrder($order);
            $orderId = $order->getId();
            if ($orderId) {
                $this->addFieldToFilter($this->_orderField, $orderId);
            } else {
                $this->_totalRecords = 0;
                $this->_setIsLoaded(true);
            }
        } else {
            $this->addFieldToFilter($this->_orderField, $order);
        }
        return $this;
    }
}
