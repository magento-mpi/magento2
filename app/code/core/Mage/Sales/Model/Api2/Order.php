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
 * API2 class for order
 *
 * @category   Mage
 * @package    Mage_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Api2_Order extends Mage_Api2_Model_Resource_Instance
{
    /**#@+
     * Parameters' names with special meaning
     */
    const PARAM_GIFT_MESSAGE   = '_gift_message';
    const PARAM_ORDER_COMMENTS = '_order_comments';
    const PARAM_PAYMENT_METHOD = '_payment_method';
    /**#@-*/

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
     * Retrieve gift message information
     *
     * @param int $messageId Gift message identifier
     * @return array
     */
    protected function _getGiftMessageInfo($messageId)
    {
        /** @var $message Mage_GiftMessage_Model_Message */
        $message = Mage::getModel('giftmessage/message');

        if ($messageId) {
            $message->load($messageId);
        }
        return array(
            'gift_message_from' => $message->getSender(),
            'gift_message_to'   => $message->getRecipient(),
            'gift_message_body' => $message->getMessage()
        );
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
            Mage_Sales_Model_Api2_Order_Items::PARAM_ORDER_ID => $orderId
        ));

        return $itemsModel->dispatch();
    }

    /**
     * Retrieve payment method information for specified order
     *
     * @param int $orderId Order identifier
     * @return string
     */
    protected function _getPaymentMethodInfo($orderId)
    {
        /** @var $payment Mage_Sales_Model_Order_Payment */
        $payment = Mage::getModel('sales/order_payment');

        $payment->load($orderId, 'parent_id');

        return array('payment_method' => $payment->getMethod());
    }

    /**
     * Check gift messages information is allowed
     *
     * @return bool
     */
    protected function _isGiftMessageAllowed()
    {
        return in_array(self::PARAM_GIFT_MESSAGE, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check order comments information is allowed
     *
     * @return bool
     */
    protected function _isOrderCommentsAllowed()
    {
        return in_array(self::PARAM_ORDER_COMMENTS, $this->getFilter()->getAllowedAttributes());
    }

    /**
     * Check payment method information is allowed
     *
     * @return bool
     */
    protected function _isPaymentMethodAllowed()
    {
        return in_array(self::PARAM_PAYMENT_METHOD, $this->getFilter()->getAllowedAttributes());
    }
}
