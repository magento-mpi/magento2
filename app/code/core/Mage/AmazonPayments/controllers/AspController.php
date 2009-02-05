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

class Mage_AmazonPayments_AspController extends Mage_Core_Controller_Front_Action
{
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
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    public function getAsp()
    {
        return Mage::getSingleton('amazonpayments/payment_asp');
    }

    /**
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    private function _validateRequest($requestParams)
    {
    	Mage::getModel();
    }

    /**
     * Get singleton with Checkout by Amazon order transaction information
     *
     * @return Mage_AmazonPayments_Model_Payment_CBA
     */
    public function cancelAction()
    {
        die('cancel');
        $this->_redirect('checkout/cart/');
    }
    
    /**
     * When a customer has checkout on Amazon and return with Successful payment
     *
     */
    public function successAction()
    {

    	$this->ipnAction();

    }

    /**
     * When a customer has checkout on Amazon and return with Successful payment
     *
     */
    public function ipnAction()
    {
        $this->getAsp()->processIpnRequest($this->getRequest()->getParams());
    	
        /*if (!$this->getRequest()->isPost()) {
            $this->_redirect('');
            return;
        }

        $this->getAsp()->processIpnRequest($this->getRequest()->isPost());
    	*/
    }
    

}