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
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Api2
 * @copyright   Copyright (c) 2011 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Abstract API2 class for order item
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
abstract class Mage_Sales_Model_Api2_Order_Rest extends Mage_Sales_Model_Api2_Order
{

    /**
     * Retrieve order addresses if allowed
     *
     * @param int $orderId Order identifier
     * @return array
     */
    protected function _getAddresses($orderId)
    {
        if (!$this->_isSubCallAllowed('addresses')) {
            return array();
        }
        /** @var $addressModel Mage_Sales_Model_Api2_Order_Addresses */
        $addressModel = $this->_getSubModel('addresses', array(
            Mage_Sales_Model_Api2_Order_Addresses_Rest::PARAM_ORDER_ID => $orderId
        ));

        return $addressModel->dispatch();
    }

    /**
     * Retrieve order comments
     *
     * @param int $orderId Order identifier
     * @return array
     */
    protected function _getComments($orderId)
    {
        if (!$this->_isSubCallAllowed('order_comments')) {
            return array();
        }
        /** @var $commentsModel Mage_Sales_Model_Api2_Order_Comments */
        $commentsModel = $this->_getSubModel('order_comments', array(
            Mage_Sales_Model_Api2_Order_Comments_Rest::PARAM_ORDER_ID => $orderId
        ));

        return $commentsModel->dispatch();
    }

    /**
     * Retrieve order items if allowed
     *
     * @param int $orderId Order identifier
     * @return array
     */
    protected function _getItems($orderId)
    {
        if (!$this->_isSubCallAllowed('order_items')) {
            return array();
        }
        /** @var $itemsModel Mage_Sales_Model_Api2_Order_Items */
        $itemsModel = $this->_getSubModel('order_items', array(
            Mage_Sales_Model_Api2_Order_Items_Rest::PARAM_ORDER_ID => $orderId
        ));

        return $itemsModel->dispatch();
    }

    /**
     * Retrieve information about specified order item
     *
     * @throws Mage_Api2_Exception
     * @return array
     */
    protected function _retrieve()
    {
        $orderId = $this->getRequest()->getParam('id');

        /* @var $order Mage_Sales_Model_Order */
        $order      = $this->_loadOrderById($orderId);
        $orderData  = $order->getData();
        $addresses  = $this->_getAddresses($orderId);
        $orderItems = $this->_getItems($orderId);

        if ($this->_isPaymentMethodAllowed()) {
            $orderData += $this->_getPaymentMethodInfo($orderId);
        }
        if (is_array($addresses) && $addresses && reset($addresses)) {
            $orderData['addresses'] = $addresses;
        }
        if (is_array($orderItems) && $orderItems && reset($orderItems)) {
            $orderData['order_items'] = $orderItems;
        }
        if ($this->_isGiftMessageAllowed()) {
            $orderData += $this->_getGiftMessageInfo($orderData['gift_message_id']);
        }
        if ($this->_isOrderCommentsAllowed()) {
            if (($comments = $this->_getComments($orderId))) {
                $orderData['order_comments'] = $comments;
            }
        }
        return $orderData;
    }

    /**
     * Load order by id
     *
     * @param int $id
     * @throws Mage_Api2_Exception
     * @return Mage_Sales_Model_Order
     */
    protected function _loadOrderById($id)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = Mage::getModel('sales/order')->load($id);
        if (!$order->getId()) {
            $this->_critical(self::RESOURCE_NOT_FOUND);
        }
        return $order;
    }
}
