<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Tax Total Row Renderer
 */
namespace Magento\Tax\Block\Checkout;

class Tax extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * @var string
     */
    protected $_template = 'checkout/tax.phtml';
}
