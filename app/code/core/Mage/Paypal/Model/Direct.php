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
 * PayPal Direct Module
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Paypal_Model_Direct extends Mage_Paypal_Model_Abstract
{
    public function createFormBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/form_cc', $name)
            ->setMethod('paypal_direct')
            ->setPayment($this->getPayment());
        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setPayment($this->getPayment());
        return $block;
    }

    public function getPaymentAction()
    {
        $paymentAction = Mage::getStoreConfig('payment/paypal_direct/payment_action');
        if (!$paymentAction) {
            $paymentAction = Mage_Paypal_Model_Api_Nvp::PAYMENT_TYPE_AUTH;
        }
        return $paymentAction;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $api = $this->getApi()
            ->setPaymentType($this->getPaymentAction())
            ->setAmount($payment->getOrder()->getGrandTotal())
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setPayment($payment);
        ;

        if ($api->callDoDirectPayment()!==false) {
            $payment
                ->setStatus('APPROVED')
                ->setCcTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());

            $payment->getOrder()->addStatus(Mage::getStoreConfig('payment/paypal_direct/order_status'));
        } else {
            $e = $api->getError();
            $payment
                ->setStatus('ERROR')
                ->setStatusDescription($e['short_message'].': '.$e['long_message']);
        }
        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }
}