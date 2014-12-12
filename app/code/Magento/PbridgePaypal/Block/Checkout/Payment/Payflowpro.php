<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

/**
 * Payflow Pro payment block
 */
namespace Magento\PbridgePaypal\Block\Checkout\Payment;

class Payflowpro extends \Magento\Pbridge\Block\Payment\Form\AbstractForm
{
    /**
     * Paypal payment code
     *
     * @var string
     */
    protected $_code = 'payflowpro';
}
