<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance total block for checkout
 *
 */
namespace Magento\CustomerBalance\Block\Checkout;

class Total extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * @var string
     */
    protected $_template = 'Magento_CustomerBalance::checkout/total.phtml';
}
