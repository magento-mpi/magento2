<?php
/**
 * Multiple address shipping checkout
 *
 * @package     Mage
 * @subpackage  Checkout
 * @copyright   Varien (c) 2007 (http://www.varien.com)
 * @license     http://www.opensource.org/licenses/osl-3.0.php
 * @author      Dmitriy Soroka <dmitriy@varien.com>
 */
class Mage_Checkout_MultiaddressController extends Mage_Core_Controller_Front_Action
{
    /**
     * Action predispatch
     * 
     * Check customer authentication for some actions
     */
    public function preDispatch()
    {
        parent::preDispatch();
        $action = $this->getRequest()->getActionName();
        if (!preg_match('#^(login|register)#', $action)) {
            $loginUrl = Mage::getUrl('*/*/login', array('_secure'=>true, '_current'=>true));
            if (!Mage::getSingleton('customer/session')->authenticate($this, $loginUrl)) {
                $this->setFlag('', 'no-dispatch', true);
            }
        }
    }
    
    /**
     * Index action of Multiaddress checkout
     */
    public function indexAction()
    {
        $this->_redirect('*/*/addresses');
    }
    
    /**
     * Multiaddress checkout login page
     */
    public function loginAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
            return;
        }
        
        $this->loadLayout(array('default', 'customer_login'), 'customer_login');
        $this->_initLayoutMessages('customer/session');
        
        // set account create url
        if ($loginForm = $this->getLayout()->getBlock('customer_form_login')) {
            $loginForm->setCreateAccountUrl(Mage::getUrl('*/*/register'));
        }
        $this->renderLayout();
    }
    
    /**
     * Multiaddress checkout login page
     */
    public function registerAction()
    {
        if (Mage::getSingleton('customer/session')->isLoggedIn()) {
            $this->getResponse()->setRedirect(Mage::getUrl('*/*/index'));
            return;
        }
        
        $this->loadLayout(array('default', 'customer_register'), 'customer_register');
        $this->_initLayoutMessages('customer/session');
        
        if ($registerForm = $this->getLayout()->getBlock('customer_form_register')) {
            $registerForm->setShowAddressFields(true)
                ->setBackUrl(Mage::getUrl('*/*/login'));
        }
        
        $this->renderLayout();
    }

    /**
     * Multiaddress checkout select address page
     */
    public function addressesAction()
    {
        $this->loadLayout(array('default', 'multiaddress', 'multiaddress_addresses'), 'multiaddress_addresses');
        $this->renderLayout();
    }
    
    /**
     * Multiaddress checkout shipping information page
     */
    public function shippingAction()
    {
        
    }
    
    /**
     * Multiaddress checkout billing information page
     */
    public function paymentAction()
    {
        
    }
    
    /**
     * Multiaddress checkout place order page
     */
    public function overviewAction()
    {
        
    }
    
    /**
     * Multiaddress checkout succes page
     */
    public function successAction()
    {
        
    }
}
