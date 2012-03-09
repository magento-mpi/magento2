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
