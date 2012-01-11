<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Captcha User Login Observer
 *
 * @category    Mage
 * @package     Mage_Captcha
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Captcha_Model_Observer_UserLogin extends Mage_Captcha_Model_Observer_AbstractCustomer
{
    /**
     * @var string
     */
    protected $_formId = 'user_login';

    /**
     * Setup Redirect if Captcha Wrong
     *
     * @param Mage_Core_Controller_Varien_Action $controller
     */
    protected function _setupRedirect($controller)
    {
        $this->_getSession()->addError(Mage::helper('Mage_Captcha_Helper_Data')->__('Incorrect CAPTCHA.'));
        $controller->setFlag('', Mage_Core_Controller_Varien_Action::FLAG_NO_DISPATCH, true);
        $login = $controller->getRequest()->getPost('login');
        $this->_getSession()->setUsername($login['username']);
        $beforeUrl = $this->_getSession()->getBeforeAuthUrl();
        $url =  $beforeUrl ? $beforeUrl : Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl();
        $controller->getResponse()->setRedirect($url);
    }
}
