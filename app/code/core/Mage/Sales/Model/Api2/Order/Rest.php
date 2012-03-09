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
