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
 * Order API
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Sales_Model_Order_Api extends Magento_Sales_Model_Api_Resource
{
    /**
     * Design package instance
     *
     * @var Magento_Core_Model_View_DesignInterface
     */
    protected $_design = null;

    /**
     * Initialize attributes map
     *
     * @param Magento_Core_Model_View_DesignInterface $design
     * @param Magento_Api_Helper_Data $apiHelper
     */
    public function __construct(
        Magento_Core_Model_View_DesignInterface $design,
        Magento_Api_Helper_Data $apiHelper
    ) {
        $this->_design = $design;
        parent::__construct($apiHelper);
        $this->_attributesMap = array(
            'order' => array('order_id' => 'entity_id'),
            'order_address' => array('address_id' => 'entity_id'),
            'order_payment' => array('payment_id' => 'entity_id')
        );
    }

    /**
     * Initialize basic order model
     *
     * @param mixed $orderIncrementId
     * @return Magento_Sales_Model_Order
     */
    protected function _initOrder($orderIncrementId)
    {
        $order = Mage::getModel('Magento_Sales_Model_Order');

        /* @var $order Magento_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        return $order;
    }

    /**
     * Retrieve list of orders. Filtration could be applied
     *
     * @param null|object|array $filters
     * @return array
     */
    public function items($filters = null)
    {
        $orders = array();

        //TODO: add full name logic
        $billingAliasName = 'billing_o_a';
        $shippingAliasName = 'shipping_o_a';

        /** @var $orderCollection Magento_Sales_Model_Resource_Order_Collection */
        $orderCollection = Mage::getModel('Magento_Sales_Model_Order')->getCollection();
        $billingFirstnameField = "$billingAliasName.firstname";
        $billingLastnameField = "$billingAliasName.lastname";
        $shippingFirstnameField = "$shippingAliasName.firstname";
        $shippingLastnameField = "$shippingAliasName.lastname";
        $billingNameExpr = "CONCAT({{billing_firstname}}, CONCAT(' ', {{billing_lastname}}))";
        $shippingNameExpr = "CONCAT({{shipping_firstname}}, CONCAT(' ', {{shipping_lastname}}))";
        $orderCollection->addAttributeToSelect('*')
            ->addAddressFields()
            ->addExpressionFieldToSelect('billing_firstname', "{{billing_firstname}}",
                array('billing_firstname' => $billingFirstnameField))
            ->addExpressionFieldToSelect('billing_lastname', "{{billing_lastname}}",
                array('billing_lastname' => $billingLastnameField))
            ->addExpressionFieldToSelect('shipping_firstname', "{{shipping_firstname}}",
                array('shipping_firstname' => $shippingFirstnameField))
            ->addExpressionFieldToSelect('shipping_lastname', "{{shipping_lastname}}",
                array('shipping_lastname' => $shippingLastnameField))
            ->addExpressionFieldToSelect('billing_name', $billingNameExpr,
                array('billing_firstname' => $billingFirstnameField, 'billing_lastname' => $billingLastnameField))
            ->addExpressionFieldToSelect('shipping_name', $shippingNameExpr,
                array('shipping_firstname' => $shippingFirstnameField, 'shipping_lastname' => $shippingLastnameField)
        );

        /** @var $apiHelper Magento_Api_Helper_Data */
        $apiHelper = Mage::helper('Magento_Api_Helper_Data');
        $filters = $apiHelper->parseFilters($filters, $this->_attributesMap['order']);
        try {
            foreach ($filters as $field => $value) {
                $orderCollection->addFieldToFilter($field, $value);
            }
        } catch (Magento_Core_Exception $e) {
            $this->_fault('filters_invalid', $e->getMessage());
        }
        foreach ($orderCollection as $order) {
            $orders[] = $this->_getAttributes($order, 'order');
        }
        return $orders;
    }

    /**
     * Retrieve full order information
     *
     * @param string $orderIncrementId
     * @return array
     */
    public function info($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if ($order->getGiftMessageId() > 0) {
            $order->setGiftMessage(
                Mage::getSingleton('Magento_GiftMessage_Model_Message')->load($order->getGiftMessageId())->getMessage()
            );
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            if ($item->getGiftMessageId() > 0) {
                $item->setGiftMessage(
                    Mage::getSingleton('Magento_GiftMessage_Model_Message')->load($item->getGiftMessageId())->getMessage()
                );
            }

            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getAllStatusHistory() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }

    /**
     * Change order status and optionally log status change with comment.
     *
     * @param string $orderIncrementId
     * @param string $status New order status
     * @param string $comment Comment to order status change
     * @param boolean $notify Should customer be notified about status change
     * @return boolean Is method executed successfully
     */
    public function addComment($orderIncrementId, $status, $comment = '', $notify = false)
    {
        $order = $this->_initOrder($orderIncrementId);

        $historyItem = $order->addStatusHistoryComment($comment, $status);
        $historyItem->setIsCustomerNotified($notify)->save();

        try {
            if ($notify && $comment) {
                $oldArea = $this->_design->getArea();
                $this->_design->setArea('frontend');
            }

            $order->save();
            $order->sendOrderUpdateEmail($notify, $comment);
            if ($notify && $comment) {
                $this->_design->setArea($oldArea);
            }

        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Hold order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function hold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->hold();
            $order->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Unhold order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function unhold($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        try {
            $order->unhold();
            $order->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }

        return true;
    }

    /**
     * Cancel order
     *
     * @param string $orderIncrementId
     * @return boolean
     */
    public function cancel($orderIncrementId)
    {
        $order = $this->_initOrder($orderIncrementId);

        if (Magento_Sales_Model_Order::STATE_CANCELED == $order->getState()) {
            $this->_fault('status_not_changed');
        }
        try {
            $order->cancel();
            $order->save();
        } catch (Magento_Core_Exception $e) {
            $this->_fault('status_not_changed', $e->getMessage());
        }
        if (Magento_Sales_Model_Order::STATE_CANCELED != $order->getState()) {
            $this->_fault('status_not_changed');
        }
        return true;
    }

} // Class Magento_Sales_Model_Order_Api End
