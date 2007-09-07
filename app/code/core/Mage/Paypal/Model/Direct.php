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
            ->setPayment($this->getPayment())
            ->setHidden($hidden);
        return $block;
    }

    public function createInfoBlock($name)
    {
        $block = $this->getLayout()->createBlock('payment/info_cc', $name)
            ->setPayment($this->getPayment());
        return $block;
    }

    public function onOrderValidate(Mage_Sales_Model_Order_Payment $payment)
    {
        $a = $this->getApi();
        $a
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setPayment($payment)
            ->callDoDirectPayment();

        $payment
            ->setTransId($a->getTransactionId())
            ->setCcAvsStatus($a->getAvsCode())
            ->setCcCidStatus($a->getCvv2Match());

        return $this;
    }

    public function onInvoiceCreate(Mage_Sales_Model_Invoice_Payment $payment)
    {

    }
}