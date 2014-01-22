<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Payflow Pro payment block
 */
namespace Magento\Pbridge\Block\Checkout\Payment;

class Payflowpro extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = 'payflowpro';
}
