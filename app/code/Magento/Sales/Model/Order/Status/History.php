<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Order\Status;

/**
 * Order status history comments
 *
 * @method \Magento\Sales\Model\Resource\Order\Status\History _getResource()
 * @method \Magento\Sales\Model\Resource\Order\Status\History getResource()
 * @method int getParentId()
 * @method \Magento\Sales\Model\Order\Status\History setParentId(int $value)
 * @method int getIsCustomerNotified()
 * @method int getIsVisibleOnFront()
 * @method \Magento\Sales\Model\Order\Status\History setIsVisibleOnFront(int $value)
 * @method string getComment()
 * @method \Magento\Sales\Model\Order\Status\History setComment(string $value)
 * @method string getStatus()
 * @method \Magento\Sales\Model\Order\Status\History setStatus(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order\Status\History setCreatedAt(string $value)
 */
class History extends \Magento\Sales\Model\AbstractModel
{
    const CUSTOMER_NOTIFICATION_NOT_APPLICABLE = 2;

    /**
     * Order instance
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order;

    /**
     * @var string
     */
    protected $_eventPrefix = 'sales_order_status_history';

    /**
     * @var string
     */
    protected $_eventObject = 'status_history';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct(
            $context,
            $registry,
            $localeDate,
            $dateTime,
            $resource,
            $resourceCollection,
            $data
        );
        $this->_storeManager = $storeManager;
    }

    /**
     * Initialize resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order\Status\History');
    }

    /**
     * Set order object and grab some metadata from it
     *
     * @param \Magento\Sales\Model\Order $order
     * @return $this
     */
    public function setOrder(\Magento\Sales\Model\Order $order)
    {
        $this->_order = $order;
        $this->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Notification flag
     *
     * @param  mixed $flag OPTIONAL (notification is not applicable by default)
     * @return $this
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
     * @return \Magento\Sales\Model\Order
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
     * @return \Magento\Store\Model\Store
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
     * @return $this
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
