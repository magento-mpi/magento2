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
 * Express Checkout Controller
 *
 * @author     Moshe Gurvich <moshe@varien.com>
 */
class Mage_Paypal_ExpressController extends Mage_Core_Controller_Front_Action
{
    /**
     * When there's an API error
     *
     */
    public function errorAction()
    {
        #$this->getRequest()->setRedirect($this->getExpress()->getApi()->getApiErrorUrl());
    }

    public function cancelAction()
    {
        $this->_redirect('checkout/cart');
    }

    /**
     * When a customer clicks Paypal button on shopping cart
     *
     */
    public function shortcutAction()
    {
        $ec = Mage::getSingleton('paypal/express');
        $ec->shortcutSetExpressCheckout();
        $this->getResponse()->setRedirect($ec->getRedirectUrl());
    }

    /**
     * When a customer chooses Paypal on Checkout/Payment page
     *
     */
    public function markAction()
    {
        $ec = Mage::getSingleton('paypal/express');
        $ec->markSetExpressCheckout();
        $this->getResponse()->setRedirect($ec->getRedirectUrl());
    }

    /**
     * Return here from Paypal before final payment (continue)
     *
     */
    public function returnAction()
    {
        $ec = Mage::getSingleton('paypal/express');
        $ec->returnFromPaypal();
        $this->getResponse()->setRedirect($ec->getRedirectUrl());
    }

    /**
     * Return here from Paypal after final payment (commit) or after on-site order review
     *
     */
    public function reviewAction()
    {
        $ec = Mage::getSingleton('paypal/express');
        $ec->commitUserAction();
        $this->getResponse()->setRedirect($ec->getRedirectUrl());
    }
}