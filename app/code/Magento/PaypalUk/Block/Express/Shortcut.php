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
 * Paypal expess checkout shortcut link
 */
namespace Magento\PaypalUk\Block\Express;

class Shortcut extends \Magento\Paypal\Block\Express\Shortcut
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_paymentMethodCode = \Magento\Paypal\Model\Config::METHOD_WPP_PE_EXPRESS;

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
    protected $_checkoutType = '\Magento\PaypalUk\Model\Express\Checkout';
}
