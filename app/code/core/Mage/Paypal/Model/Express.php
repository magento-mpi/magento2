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

/**
 *
 * PayPal Express Checkout Module
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Paypal_Model_Express extends Mage_Paypal_Model_Abstract
{
    public function catchError()
    {
        if ($this->getApi()->hasError()) {
            $s = Mage::getSingleton('checkout/session');
            $e = $this->getApi()->getError();
            switch ($e['type']) {
                case 'CURL':
                    $s->addError(__('There was an error connecting to the Paypal server: %s', $e['message']));
                    break;

                case 'API':
                    $s->addError(__('There was an error during communication with Paypal: %s - %s', $e['short_message'], $e['long_message']));
                    break;
            }
        }
        return $this;
    }

    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/express_form', $name)
            ->setMethod('paypal_express')
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/form.phtml');

        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('paypal/express_info', $name)
            ->setPayment($this->getPayment())
            ->setTemplate('paypal/express/info.phtml');
        return $block;
    }

    public function getCheckoutRedirectUrl()
    {
        return Mage::getUrl('paypal/express/mark');
    }

    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/paypal_express/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    public function shortcutSetExpressCheckout()
    {
        $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($this->getQuote()->getGrandTotal())
            ->setCurrencyCode($this->getQuote()->getStoreCurrencyCode())
            ->callSetExpressCheckout();

        $this->catchError();

        return $this;
    }

    public function markSetExpressCheckout()
    {
        $address = $this->getQuote()->getShippingAddress();
        $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($address->getGrandTotal())
            ->setCurrencyCode($this->getQuote()->getStoreCurrencyCode())
            ->setShippingAddress($address)
            ->callSetExpressCheckout();

        $this->catchError();

        return $this;
    }

    public function returnFromPaypal()
    {
        switch ($this->getApi()->getUserAction()) {
            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_CONTINUE:
                $this->_getExpressCheckoutDetails();
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/review'));
                break;

            case Mage_Paypal_Model_Api_Nvp::USER_ACTION_COMMIT:
                $this->getApi()->setRedirectUrl(Mage::getUrl('paypal/express/saveOrder'));
                break;
        }
        return $this;
    }

    protected function _getExpressCheckoutDetails()
    {
        $api = $this->getApi();
        if (!$api->callGetExpressCheckoutDetails()) {
            Mage::throwException(__('Problem during communication with PayPal'));
        }
        $q = $this->getQuote();
        $a = $api->getShippingAddress();

        $a->setCountryId(
            Mage::getModel('directory/country')->loadByCode($a->getCountry())->getId()
        );
        $a->setRegionId(
            Mage::getModel('directory/region')->loadByCode($a->getRegion(), $a->getCountryId())->getId()
        );

        $q->getBillingAddress()
            ->setFirstname('Paypal')
            ->setEmail($a->getEmail());

        $q->getShippingAddress()
            ->importCustomerAddress($a)
            ->setCollectShippingRates(true);

        $q->setCheckoutMethod('paypal_express');

        $q->getPayment()
            ->setMethod('paypal_express')
            ->setPaypalCorrelationId($api->getCorrelationId())
            ->setPaypalPayerId($api->getPayerId())
            ->setPaypalPayerStatus($api->getPayerStatus())
        ;

        $q->collectTotals()->save();

    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $api = $this->getApi();
        $api->setAmount($payment->getOrder()->getGrandTotal())
            ->setCurrencyCode($payment->getOrder()->getOrderCurrencyCode());

        if ($api->callDoExpressCheckoutPayment()!==false) {
            $payment->setStatus('APPROVED')
                ->setCcTransId($api->getTransactionId())
                ->setPayerId($api->getPayerId());
        } else {
            $e = $api->getError();
            $payment->setStatus('ERROR')
                ->setStatusDescription($e['short_message'].': '.$e['long_message']);
        }
        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }
}
