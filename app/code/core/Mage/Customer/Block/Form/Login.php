<?php
/**
 * Customer login form block
 *
 * @package    Mage
 * @subpackage Customer
 * @author     Dmitriy Soroka <dmitriy@varien.com>
 * @copyright  Varien (c) 2007 (http://www.varien.com)
 */
class Mage_Customer_Block_Form_Login extends Mage_Core_Block_Template
{
    public function __construct() 
    {
        parent::__construct();
        $this->setTemplate('customer/form/login.phtml');
        Mage::registry('action')->getLayout()->getBlock('root')->setHeaderTitle(__('Login'));
    }
    
    /**
     * Retrieve form posting url
     *
     * @return string
     */
    public function getPostActionUrl()
    {
        return Mage::getUrl('customer/account/loginPost', array('_secure'=>true));
    }
    
    /**
     * Retrieve create new account url
     *
     * @return string
     */
    public function getCreateAccountUrl()
    {
        $url = $this->getData('create_account_url');
        if (is_null($url)) {
            $url = $this->getUrl('customer/account/create');
        }
        return $url;
    }
    
    /**
     * Retrieve password forgotten url
     *
     * @return string
     */
    public function getForgotPasswordUrl()
    {
        return $this->getUrl('customer/account/forgotpassword');
    }
    
    /**
     * Retrieve username for form field
     *
     * @return string
     */
    public function getUsername()
    {
        return Mage::getSingleton('customer/session')->getUsername(true);
    }
}
