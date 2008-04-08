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
 * @package    Mage_Eway
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 *
 * Eway Direct Module
 *
 */
class Mage_Eway_Model_Direct extends Mage_Payment_Model_Method_Cc
{
    protected $_code  = 'eway_direct';

    protected $_isGateway               = true;
    protected $_canAuthorize            = false;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
    protected $_canVoid                 = false;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = true;

    protected $_formBlockType = 'eway/form';
    protected $_infoBlockType = 'eway/info';

    /**
     * Get Eway API Model
     *
     * @return Mage_Eway_Model_Api_Api
     */
    public function getApi()
    {
        return Mage::getSingleton('eway/api_api');
    }

    /**
     * Get eway session namespace
     *
     * @return Mage_Eway_Model_Session
     */
    public function getSession()
    {
        return Mage::getSingleton('eway/session');
    }

    /**
     * Get checkout session namespace
     *
     * @return Mage_Checkout_Model_Session
     */
    public function getCheckout()
    {
        return Mage::getSingleton('checkout/session');
    }

    /**
     * Get current quote
     *
     * @return Mage_Sales_Model_Quote
     */
    public function getQuote()
    {
        return $this->getCheckout()->getQuote();
    }

    public function capture(Varien_Object $payment, $amount)
    {
        $api = $this->getApi()
            ->setAmount($amount)
            ->setBillingAddress($payment->getOrder()->getBillingAddress())
            ->setPayment($payment)
            ->setQuote($this->getQuote());

        $result = $api->callDoDirectPayment()!==false;
        
        if ($result) {
            $payment
                ->setStatus('APPROVED')
                ->setLastTransId($api->getTransactionId())
                ->setCcAvsStatus($api->getAvsCode())
                ->setCcCidStatus($api->getCvv2Match());
        } else {
            $e = $api->getError();
            $message = Mage::helper('eway')->__('There has been an error processing your payment. Please try later or contact us for help.');
            if (isset($e['long_message'])) {
                $message .= ': '.$e['long_message'];
            }
            Mage::throwException($message);
        }
        return $this;
    }

}