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
 *
 * @category   Magento
 * @package    Magento_Rma
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Rma\Model\Rma;

class Create extends \Magento\Object
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
                $customer = \Mage::getModel('Magento\Customer\Model\Customer');
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
                $order = \Mage::getModel('Magento\Sales\Model\Order');
                $order->load($orderId);
                $this->_order = $order;
            }
        }
        return $this->_order;
    }
}
