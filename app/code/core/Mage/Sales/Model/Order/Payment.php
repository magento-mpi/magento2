<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Sales
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Order payment information
 */
class Mage_Sales_Model_Order_Payment extends Mage_Payment_Model_Info
{
    /**
     * Order model object
     *
     * @var Mage_Sales_Model_Order
     */
    protected $_order;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_payment');
    }

    /**
     * Declare order model object
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Payment
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        return $this;
    }

    /**
     * Retrieve order model object
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Place payment information
     *
     * This method are colling when order will be place
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function place()
    {
        $methodInstance = $this->getMethodInstance();
        if ($action = $methodInstance->getConfigData('payment_action')) {
            /**
             * Run action declared for payment method in configuration
             */
            switch ($action) {
                case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE:
                    $methodInstance->authorize($this, $this->getOrder()->getTotalDue());
                    break;
                case Mage_Payment_Model_Method_Abstract::ACTION_AUTHORIZE_CAPTURE:
                    $methodInstance->authorize($this, $this->getOrder()->getTotalDue());
                    $methodInstance->capture($this, $this->getOrder()->getTotalDue());
                    break;
                default:
                    break;
            }
        }

        /**
         * Change order status if it specified
         */
        if ($newOrderStatus = $methodInstance->getConfigData('order_status')) {
            $this->getOrder()->addStatusToHistory(
                $newOrderStatus,
                Mage::helper('sales')->__('Change status based on configuration')
            );
        }

        return $this;
    }

    /**
     * Capture payment
     *
     * @return Mage_Sales_Model_Order_Payment
     */
    public function capture($invoice)
    {

        return $this;
    }

    public function void()
    {
        return $this;
    }

    public function refound()
    {
        return $this;
    }

    public function cancel()
    {
        return $this;
    }
}