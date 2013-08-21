<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Express Checkout Controller
 */

class Magento_PaypalUk_Controller_Express extends Magento_Paypal_Controller_Express_Abstract
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
    protected $_configMethod = Magento_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Magento_PaypalUk_Model_Express_Checkout';
}
