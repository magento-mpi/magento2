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

class Mage_AmazonPayments_CbaController extends Mage_Core_Controller_Front_Action
{
    /**
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    public function getCba()
    {
        return Mage::getSingleton('amazonpayments/payment_cba');
    }

    /**
     * When a customer clicks Checkout with Amazon button on shopping cart
     */
    public function shortcutAction()
    {
        if (!$this->getCba()->isAvailable()) {
            $this->_redirect('checkout/cart/');
        }
        $this->getCba()->shortcutSetCbaCheckout();
        $this->getResponse()->setRedirect($this->getCba()->getRedirectUrl());
    }

    /**
     * When a customer chooses Checkout by Amazon on Checkout/Payment page
     *
     */
    public function redirectAction()
    {
        if (!$this->getCba()->isAvailable()) {
            $this->_redirect('checkout/cart/');
        }
        $session = Mage::getSingleton('checkout/session');
        $session->setAmazonCbaQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('amazonpayments/cba_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer has checkout on Amazon and return with Success
     *
     */
    public function successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $amazonCbaQuoteId = $session->getAmazonCbaQuoteId();
        //echo "amazonCbaQuoteId: {$amazonCbaQuoteId}<br />";

        /*
        $_requestParams = Mage::app()->getRequest()->getParams();
        Array
        (
            [amznPmtsOrderIds] => 102-7389301-2720225
            [showAmznPmtsTYPopup] => 1
            [merchName] => Varien
            [amznPmtsYALink] => http://kv.no-ip.org/dev/andrey.babich/magento/index.php/amazonpayments/cba/return/?amznPmtsOrderIds=102-7389301-272022&
        )
        */

        $amazonOrderDetails = $this->getCba()->getAmazonOrderDetails();

        $this->_redirect('checkout/onepage/success');
    }

    /**
     * When a customer has checkout on Amazon and return with Cancel
     *
     */
    public function cancelAction()
    {
        $this->_redirect('checkout/cart/');
    }
}