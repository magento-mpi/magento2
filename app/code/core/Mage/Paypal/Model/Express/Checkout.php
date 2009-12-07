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
 * @package     Mage_Paypal
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Wrapper that performs Paypal Express and Checkout communication
 * Use current Paypal Express method instance
 */
class Mage_Paypal_Model_Express_Checkout
{
    /**
     * Perform API call to start transaction from shopping cart
     *
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function shortcutSetExpressCheckout($express)
    {
        $api = $express->getApi();
        $express->getQuote()->reserveOrderId()->save();
        $api->setSolutionType($express->getSolutionType())
            ->setPayment($express->getPayment())
            ->setPaymentType($express->getPaymentAction())
            ->setAmount($express->getQuote()->getBaseGrandTotal())
            ->setCurrencyCode($express->getQuote()->getBaseCurrencyCode())
            ->setInvNum($express->getQuote()->getReservedOrderId());

        $api->callSetExpressCheckout();

        $express->catchError();

        $express->getSession()->setExpressCheckoutMethod('shortcut');

        return $this;
    }

    /**
     * Perform API call to check transaction's status when customer returns from paypal
     *
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function returnFromPaypal($express)
    {
        try {
            $this->getExpressCheckoutDetails($express);
        } catch (Exception $e) {
            $express->getSession()->addError($e->getMessage());
            $express->getApi()->setRedirectUrl('paypal/express/review');
        }

        switch ($express->getApi()->getUserAction()) {
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_CONTINUE:
                $express->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/review'));
                break;
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_COMMIT:
                if ($express->getSession()->getExpressCheckoutMethod() == 'shortcut') {
                    $express->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/saveOrder'));
                } else {
                    $express->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/updateOrder'));
                }
                break;
        }
        return $this;
    }

    /**
     * Save shipping to quote on the Order review page
     *
     * @param string $shippingMethod
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return array
     */
    public function saveShippingMethod($shippingMethod, $express)
    {
        if (empty($shippingMethod)) {
            $res = array(
                'error' => -1,
                'message' => Mage::helper('paypal')->__('Invalid Shipping Method')
            );
            return $res;
        }

        $express->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod)
            ->setCollectShippingRates(true);
        $express->getQuote()->collectTotals()->save();
        return array();
    }

    /**
     * Perform API call for checkout details and update quote, payment etc.
     *
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function getExpressCheckoutDetails($express)
    {
        $api = $express->getApi();
        $api->setPayment($express->getPayment());
        if (!$api->callGetExpressCheckoutDetails()) {
            Mage::throwException(Mage::helper('paypal')->__('Problem during communication with PayPal'));
        }
        $q = $express->getQuote();
        $a = $api->getShippingAddress();

        $a->setCountryId(
            Mage::getModel('directory/country')->loadByCode($a->getCountry())->getId()
        );
        $a->setRegionId(
            Mage::getModel('directory/region')->loadByCode($a->getRegion(), $a->getCountryId())->getId()
        );

        /*
        we want to set the billing information
        only if the customer checkout from shortcut(shopping cart) or
        if the customer checkout from mark(one page) and guest
        */
        $method = $express->getSession()->getExpressCheckoutMethod();

        if ($method == 'shortcut'
            || ($method != 'shortcut' && $q->getCheckoutMethod() != Mage_Sales_Model_Quote::CHECKOUT_METHOD_REGISTER))
        {
            $q->getBillingAddress()
                ->setPrefix($a->getPrefix())
                ->setFirstname($a->getFirstname())
                ->setMiddlename($a->getMiddlename())
                ->setLastname($a->getLastname())
                ->setSuffix($a->getSuffix())
                ->setEmail($a->getEmail());
        }

        if ($method == 'shortcut') {
            $q->getBillingAddress()->importCustomerAddress($a);
        }

        $q->getShippingAddress()
            ->importCustomerAddress($a)
            ->setCollectShippingRates(true);

        //$q->setCheckoutMethod('paypal_express');

        $q->getPayment()
            ->setMethod('paypal_express')
            ->setPaypalCorrelationId($api->getCorrelationId())
            ->setPaypalPayerId($api->getPayerId())
            ->setAddressStatus($api->getAddressStatus())
            ->setPaypalPayerStatus($api->getPayerStatus())
            ->setAccountStatus($api->getAccountStatus())
            ->setAdditionalData($api->getPaypalPayerEmail());

        if ($express->canStoreFraud()) {
            $q->getPayment()->setFraudFlag(true);
        }

        $q->collectTotals()->save();

        return $this;
    }

    /**
     * Creating order processing based on quote convert
     * Save additional transaction details to session
     *
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function saveOrder($express)
    {
        $service = Mage::getModel('sales/service_quote', $express->getQuote());
        $order = $service->submit();

        //@todo, bug: email send flag not set.
        if ($order->hasInvoices() && $express->canSendEmailCopy()) {
            foreach ($order->getInvoiceCollection() as $invoice) {
                $invoice->sendEmail()->setEmailSent(true);
            }
        }

        $order->sendNewOrderEmail();

        $express->getQuote()->save();

        $express->getCheckout()->setLastQuoteId($express->getQuote()->getId())
            ->setLastSuccessQuoteId($express->getQuote()->getId())
            ->setLastOrderId($order->getId())
            ->setLastRealOrderId($order->getIncrementId());

        return $this;
    }

    /**
     * Do order update after return from PayPal
     *
     * @param int $orderId
     * @param Mage_Paypal_Model_Express $express Paypal Express method instance object
     * @return Mage_Paypal_Model_Express_Checkout
     */
    public function updateOrder($orderId, $express)
    {
        $order = Mage::getModel('sales/order')->load($orderId);
        if (!$order->getId()) {
            Mage::throwException(Mage::helper('paypal')->__('Wrong Order ID.'));
        }

        $comment = '';
        if ($order->canInvoice() && $express->getPaymentAction() == Mage_Paypal_Model_Api_Abstract::PAYMENT_TYPE_SALE) {
            $order->getPayment()->capture(null);
        } else {
            $express->placeOrder($order->getPayment());
            $comment = Mage::helper('paypal')->__('Customer returned from PayPal site.');
        }

        $order->sendNewOrderEmail();

        $history = $order->addStatusHistoryComment(Mage::helper('paypal')->__('PayPal Express processing: %s', $comment))
            ->setIsCustomerNotified(true)
            ->save();

        $order->save();

        return $this;
    }
}
