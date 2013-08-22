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
 * Order status history comments
 *
 * @method Magento_Sales_Model_Resource_Order_Status_History _getResource()
 * @method Magento_Sales_Model_Resource_Order_Status_History getResource()
 * @method int getParentId()
 * @method Magento_Sales_Model_Order_Status_History setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method int getIsVisibleOnFront()
 * @method Magento_Sales_Model_Order_Status_History setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method Magento_Sales_Model_Order_Status_History setComment(string $value)
 * @method string getStatus()
 * @method Magento_Sales_Model_Order_Status_History setStatus(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Order_Status_History setCreatedAt(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Status_History extends Magento_Sales_Model_Abstract
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Order instance
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order;

    protected $_eventPrefix = 'sales_order_status_history';
    protected $_eventObject = 'status_history';

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Order_Status_History');
    }

    /**
     * Set order object and grab some metadata from it
     *
     * @param   Magento_Sales_Model_Order $order
     * @return  Magento_Sales_Model_Order_Status_History
     */
    public function setOrder(Magento_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return Magento_Sales_Model_Order_Status_History
     */
    public function setIsCustomerNotified($flag = null)
    {
        if (is_null($flag)) {
            $flag = self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
        }

        return $this->setData('is_customer_notified', $flag);
    }

    /**
     * Customer Notification Applicable check method
     *
     * @return boolean
     */
    public function isCustomerNotificationNotApplicable()
    {
        return $this->getIsCustomerNotified() == self::CUSTOMER_NOTIFICATION_NOT_APPLICABLE;
    }

    /**
     * Retrieve order instance
     *
     * @return Magento_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Retrieve status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        if($this->getOrder()) {
            return $this->getOrder()->getConfig()->getStatusLabel($this->getStatus());
        }
    }

    /**
     * Get store object
     *
     * @return unknown
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return Mage::app()->getStore();
    }

    /**
     * Set order again if required
     *
     * @return Magento_Sales_Model_Order_Status_History
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getParentId() && $this->getOrder()) {
            $this->setParentId($this->getOrder()->getId());
        }

        return $this;
    }
}
