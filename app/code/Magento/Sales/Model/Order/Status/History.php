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
     * @var Magento_Core_Model_StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_LocaleInterface $coreLocale
     * @param Magento_Core_Model_StoreManagerInterface $storeManager
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_LocaleInterface $coreLocale,
        Magento_Core_Model_StoreManagerInterface $storeManager,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $coreLocale,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

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
        if ($this->getOrder()) {
            return $this->getOrder()->getConfig()->getStatusLabel($this->getStatus());
        }
    }

    /**
     * Get store object
     *
     * @return Magento_Core_Model_Store
     */
    public function getStore()
    {
        if ($this->getOrder()) {
            return $this->getOrder()->getStore();
        }
        return $this->_storeManager->getStore();
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
