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
class Mage_Paypal_Controller_Express extends Mage_Paypal_Controller_Express_Abstract
{
    /**
     * Config mode type
     *
     * @var string
     */
    protected $_configType = 'Mage_Paypal_Model_Config';

    /**
     * Config method type
     *
     * @var string
     */
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_WPP_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_Paypal_Model_Express_Checkout';

    /**
     * Redirect to login page
     *
     */
    public function redirectLogin()
    {
        $this->setFlag('', 'no-dispatch', true);
        Mage::getSingleton('Mage_Customer_Model_Session')->setBeforeAuthUrl($this->_getRefererUrl());
        $this->getResponse()->setRedirect(
            Mage::helper('Mage_Core_Helper_Url')->addRequestParam(
                Mage::helper('Mage_Customer_Helper_Data')->getLoginUrl(),
                array('context' => 'checkout')
            )
        );
    }
}
