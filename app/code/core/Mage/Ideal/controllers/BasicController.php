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
 * @package    Mage_Ideal
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * iDEAL Basic Checkout Controller
 *
 * @category    Mage
 * @package     Mage_Ideal
 * @author      Dmytro Vasylenko <dimav@varien.com>
 */

class Mage_Ideal_BasicController extends Mage_Core_Controller_Front_Action
{
    /**
     * When a customer chooses iDEAL Basic on Checkout/Payment page
     */
    public function redirectAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setIdealBasicQuoteId($session->getQuoteId());
        $this->getResponse()->setBody($this->getLayout()->createBlock('ideal/basic_redirect')->toHtml());
        $session->unsQuoteId();
    }

    /**
     * When a customer cancel payment from iDEAL.
     */
    public function cancelAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId(true));
        $this->_redirect('checkout/cart');
    }

    /**
     * When customer return from iDEAL
     */
    public function  successAction()
    {
        $session = Mage::getSingleton('checkout/session');
        $session->setQuoteId($session->getIdealBasicQuoteId(true));
        /**
         * set the quote as inactive after back from iDEAL
         */
        Mage::getSingleton('checkout/session')->getQuote()->setIsActive(false)->save();

        /**
         * send confirmation email to customer
         */
        $order = Mage::getModel('sales/order');

        $order->load(Mage::getSingleton('checkout/session')->getLastOrderId());
        if($order->getId()){
            $order->sendNewOrderEmail();
        }

        //Mage::getSingleton('checkout/session')->unsQuoteId();

        $this->_redirect('checkout/onepage/success', array('_secure'=>true));
    }
}
