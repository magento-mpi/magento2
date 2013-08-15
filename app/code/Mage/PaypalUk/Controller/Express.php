<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_PaypalUk
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Express Checkout Controller
 */

class Mage_PaypalUk_Controller_Express extends Mage_Paypal_Controller_Express_Abstract
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
    protected $_configMethod = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Checkout mode type
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_PaypalUk_Model_Express_Checkout';
}
