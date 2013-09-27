<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Rma
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * RMA create model
 */
class Magento_Rma_Model_Rma_Create extends Magento_Object
{
    /**
     * Customer object, RMA's order attached to
     *
     * @var Magento_Customer_Model_Customer
     */
    protected $_customer = null;

    /**
     * Order object, RMA attached to
     *
     * @var Magento_Sales_Model_Order
     */
    protected $_order = null;

    /**
     * @var Magento_Customer_Model_CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var Magento_Sales_Model_OrderFactory
     */
    protected $_orderFactory;

    /**
     * @param Magento_Customer_Model_CustomerFactory $customerFactory
     * @param Magento_Sales_Model_OrderFactory $orderFactory
     * @param array $data
     */
    public function __construct(
        Magento_Customer_Model_CustomerFactory $customerFactory,
        Magento_Sales_Model_OrderFactory $orderFactory,
        array $data = array()
    ) {
        $this->_customerFactory = $customerFactory;
        $this->_orderFactory = $orderFactory;
        parent::__construct($data);
    }

    /**
     * Get Customer object
     *
     * @param null|int $customerId
     * @return Magento_Customer_Model_Customer|null
     */
    public function getCustomer($customerId = null)
    {
        if (is_null($this->_customer)) {
            if (is_null($customerId)) {
                $customerId = $this->getCustomerId();
            }
            $customerId = intval($customerId);

            if ($customerId) {
                /** @var $customer Magento_Customer_Model_Customer */
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
     * @return Magento_Sales_Model_Order|null
     */
    public function getOrder($orderId = null)
    {
        if (is_null($this->_order)) {
            if (is_null($orderId)) {
                $orderId = $this->getOrderId();
            }
            $orderId = intval($orderId);
            if ($orderId) {
                /** @var $order Magento_Sales_Model_Order */
                $order = $this->_orderFactory->create();
                $order->load($orderId);
                $this->_order = $order;
            }
        }
        return $this->_order;
    }
}
