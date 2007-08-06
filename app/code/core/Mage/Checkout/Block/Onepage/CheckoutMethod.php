<?php
/**
 * One page checkout status
 *
 * @category   Mage
 * @package    Mage_Checkout
 * @subpackage Onepage
 * @author     Moshe Gurvich <moshe@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Checkout_Block_Onepage_CheckoutMethod extends Mage_Checkout_Block_Onepage_Abstract
{
    protected function _construct()
    {
        if (!$this->isCustomerLoggedIn()) {
            $this->getCheckout()->setStepData('checkout_method', array('label'=>__('Checkout method'), 'allow'=>true));
        }
        parent::_construct();
    }
    
    public function getMessages()
    {
        return Mage::getSingleton('customer/session')->getMessages(true);
    }
    
    public function getPostAction()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }
    
    public function getMethod()
    {
        return $this->getQuote()->getMethod();
    }
    
    public function getMethodData()
    {
        return $this->getCheckout()->getMethodData();
    }
}