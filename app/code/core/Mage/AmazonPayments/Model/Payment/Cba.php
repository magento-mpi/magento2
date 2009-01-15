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
 * @category   Mage
 * @package    Mage_AmazonPayments
 * @copyright  Copyright (c) 2008 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

class Mage_AmazonPayments_Model_Payment_CBA extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment module of Checkout by Amazon
     * CBA - Checkout By Amazon
     */

    protected $_code  = 'amazon_cba';
    protected $_formBlockType = 'amazonpayments/form/cba';
    #protected $_formBlockType = 'amazonpayments/form';

    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    public function getOrderPlaceRedirectUrl()
    {
        echo "getOrderPlaceRedirectUrl\n";
        return false; #$this->getRedirectUrl();
    }

    public function getCheckoutRedirectUrl()
    {
        echo "getCheckoutRedirectUrl\n";
        return false; #$this->getRedirectUrl();
    }

    public function getRedirectUrl()
    {
        echo "getRedirectUrl\n";
        $_url = Mage::getUrl('amazonepayments/redirect');
        return false;
    }


    public function canCapture()
    {
        return true;
    }

    /**
     * initialize payment transaction in case
     * we doing checkout through onepage checkout
     */
    public function initialize($paymentAction, $stateObject)
    {
        $address = $this->getQuote()->getBillingAddress();

        $_quote = $this->getQuote();

        $this->getApi()
            ->setPaymentType($paymentAction)
            ->setAmount($address->getBaseGrandTotal())
            ->setCurrencyCode($this->getQuote()->getBaseCurrencyCode())
            ->setBillingAddress($address)
            ->setCardId($this->getQuote()->getReservedOrderId())
            ->setCustomerName($this->getQuote()->getCustomer()->getName());
            #->callSetExpressCheckout();

        #$this->throwError();

        $stateObject->setState(Mage_Sales_Model_Order::STATE_PENDING_PAYMENT);
        $stateObject->setStatus('pending_worldpay');
        $stateObject->setIsNotified(false);

        Mage::getSingleton('worldpay/session')->unsExpressCheckoutMethod();

        return $this;
    }

    /**
     * Rewrite standard logic
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        return true;
    }
}