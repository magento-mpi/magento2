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
 * Order API
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Api extends Mage_Sales_Model_Api_Resource
{
    public function __construct()
    {
        $this->_attributesMap['order']         = array('order_id' => 'entity_id');
        $this->_attributesMap['order_item']    = array('item_id'  => 'entity_id');
        $this->_attributesMap['order_address'] = array('address_id' => 'entity_id');
        $this->_attributesMap['order_payment'] = array('payment_id' => 'entity_id');
        $this->_attributesMap['order_status_history']  = array('history_id' => 'entity_id');


    }

    public function items($filters = null)
    {
        //TODO: add full name logic
        $collection = Mage::getResourceModel('sales/order_collection')
            ->addAttributeToSelect('*')
            ->joinAttribute('billing_firstname', 'order_address/firstname', 'billing_address_id', null, 'left')
            ->joinAttribute('billing_lastname', 'order_address/lastname', 'billing_address_id', null, 'left')
            ->joinAttribute('shipping_firstname', 'order_address/firstname', 'shipping_address_id', null, 'left')
            ->joinAttribute('shipping_lastname', 'order_address/lastname', 'shipping_address_id', null, 'left')
            ->addExpressionAttributeToSelect('billing_name',
                'CONCAT({{billing_firstname}}, " ", {{billing_lastname}})',
                array('billing_firstname', 'billing_lastname'))
            ->addExpressionAttributeToSelect('shipping_name',
                'CONCAT({{shipping_firstname}}, " ", {{shipping_lastname}})',
                array('shipping_firstname', 'shipping_lastname'));


        if (is_array($filters)) {
            try {
                foreach ($filters as $field => $value) {
                    if (isset($this->_filtersMap[$field])) {
                        $field = $this->_filtersMap[$field];
                    }

                    $collection->addFieldToFilter($field, $value);
                }
            } catch (Mage_Core_Exception $e) {
                $this->_fault('filters_invalid', $e->getMessage());
            }
        }

        $result = array();

        foreach ($collection as $order) {
            $result[] = $this->_getAttributes($order, 'order');
        }

        return $result;
    }

    public function info($orderIncrementId)
    {
        $order = Mage::getModel('sales/order');

        /* @var $order Mage_Sales_Model_Order */

        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        $result = $this->_getAttributes($order, 'order');

        $result['shipping_address'] = $this->_getAttributes($order->getShippingAddress(), 'order_address');
        $result['billing_address']  = $this->_getAttributes($order->getBillingAddress(), 'order_address');
        $result['items'] = array();

        foreach ($order->getAllItems() as $item) {
            $result['items'][] = $this->_getAttributes($item, 'order_item');
        }

        $result['payment'] = $this->_getAttributes($order->getPayment(), 'order_payment');

        $result['status_history'] = array();

        foreach ($order->getStatusHistoryCollection() as $history) {
            $result['status_history'][] = $this->_getAttributes($history, 'order_status_history');
        }

        return $result;
    }

    public function addStatus($orderIncrementId, $status, $comment = null, $notify = false)
    {
        $order = Mage::getModel('sales/order');
        /* @var $order Mage_Sales_Model_Order */
        $order->loadByIncrementId($orderIncrementId);

        if (!$order->getId()) {
            $this->_fault('not_exists');
        }

        $order->addStatusToHistory($status, $comment, $notify);

        try {
            $order->save();
        } catch (Mage_Core_Exception $e) {
            $this->_fault('status_not_added');
        }

        return true;
    }

} // Class Mage_Sales_Model_Order_Api End