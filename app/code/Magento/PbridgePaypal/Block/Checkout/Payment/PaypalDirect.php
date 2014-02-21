<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Paypal Direct payment block
 */
namespace Magento\PbridgePaypal\Block\Checkout\Payment;

class PaypalDirect extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_WPP_DIRECT;
}
