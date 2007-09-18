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
 * @package    Mage_Paypal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class Mage_Paypal_Model_Express_Review
{
    /**
     * Enter description here...
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Enter description here...
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    /**
     * Enter description here...
     *
     * @param int $addressId
     * @return Mage_Customer_Model_Address
     */
    public function getAddress($addressId)
    {
        $address = Mage::getModel('customer/address')->load((int)$addressId);
        $address->explodeStreetAddress();
        if ($address->getRegionId()) {
            $address->setRegion($address->getRegionId());
        }
        return $address;
    }

    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => __('Invalid data')
            );
            return $res;
        }
        $this->getQuote()->getShippingAddress()->setShippingMethod($shippingMethod)->collectTotals()->save();
        return array();
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function saveOrder()
    {
        $res = array('error'=>true);

        try {
            $billing = $this->getQuote()->getBillingAddress();
            $shipping = $this->getQuote()->getShippingAddress();

            $order = Mage::getModel('sales/order');
            /* @var $order Mage_Sales_Model_Order */

            $order->createFromQuoteAddress($shipping);

            $order->validate();

            $order->setInitialStatus();

            $order->save();

            $this->getQuote()->setIsActive(false);
            $this->getQuote()->save();

            $orderId = $order->getIncrementId();
            $this->getCheckout()->setLastQuoteId($this->getQuote()->getId());
            $this->getCheckout()->setLastOrderId($order->getId());
            $this->getCheckout()->setLastRealOrderId($order->getIncrementId());

            $order->sendNewOrderEmail();

            $res['success'] = true;
            $res['error']   = false;
            //$res['error']   = true;
        }
        catch (Exception $e){
            $res['success'] = false;
            $res['error'] = true;
            $res['error_messages'] = $order->getErrors();
        }

        return $res;
    }
}