<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Express Checkout Controller
 */
class Magento_Paypal_Controller_Express extends Magento_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Magento_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Magento_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento_Paypal_Model_Express_Checkout';

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        Mage::getSingleton('Magento_Customer_Model_Session')->setBeforeAuthUrl($this->_getRefererUrl());
        $this->getResponse()->setRedirect(
            $this->_objectManager->get('Magento_Core_Helper_Url')->addRequestParam(
                $this->_objectManager->get('Magento_Customer_Helper_Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }
}
