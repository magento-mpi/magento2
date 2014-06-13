<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Pbridge\Block\Checkout\Payment\Sagepay;

class Direct extends \Magento\Pbridge\Block\Checkout\Payment\AbstractPayment
{
    /**
     * Whether to include shopping cart items parameters in Payment Bridge source URL
     *
     * @var bool
     */
    protected $_sendCart = true;
}
