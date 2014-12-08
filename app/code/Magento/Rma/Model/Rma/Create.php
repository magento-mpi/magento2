<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Rma\Model\Rma;

/**
 * RMA create model
 */
class Create extends \Magento\Framework\Object
{
    /**
     * Customer object, RMA's order attached to
     *
     * @var \Magento\Customer\Model\Customer
     */
    protected $_customer = null;

    /**
     * Order object, RMA attached to
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order = null;

    /**
     * Customer factory
     *
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * Sales order factory
     *
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        array $data = []
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($data);
    }

    /**
     * Get Customer object
     *
     * @param null|int $customerId
     * @return \Magento\Customer\Model\Customer|null
     */
    public function getCustomer($customerId = null)
    {
        if (is_null($this->_customer)) {
            if (is_null($customerId)) {
                $customerId = $this->getCustomerId();
            }
            $customerId = intval($customerId);

            if ($customerId) {
                /** @var $customer \Magento\Customer\Model\Customer */
                $customer = $this->_customerFactory->create();
                $customer->load($customerId);
                $this->_customer = $customer;
            } elseif (intval($this->getOrderId())) {
                return $this->getCustomer($this->getOrder()->getCustomerId());
            }
        }
        return $this->_customer;
    }

    /**
     * Get Order object
     *
     * @param null|int $orderId
     * @return \Magento\Sales\Model\Order|null
     */
    public function getOrder($orderId = null)
    {
        if (is_null($this->_order)) {
            if (is_null($orderId)) {
                $orderId = $this->getOrderId();
            }
            $orderId = intval($orderId);
            if ($orderId) {
                /** @var $order \Magento\Sales\Model\Order */
                $order = $this->_orderFactory->create();
                $order->load($orderId);
                $this->_order = $order;
            }
        }
        return $this->_order;
    }
}
