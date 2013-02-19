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
 * Paypal expess checkout shortcut link
 */
class Mage_PaypalUk_Block_Express_Shortcut extends Mage_Paypal_Block_Express_Shortcut
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode = Mage_Paypal_Model_Config::METHOD_WPP_PE_EXPRESS;

    /**
     * Start express action
     *
     * @var string
     */
    protected $_startAction = 'paypaluk/express/start';

    /**
     * Express checkout model factory name
     *
     * @var string
     */
    protected $_checkoutType = 'Mage_PaypalUk_Model_Express_Checkout';
}
