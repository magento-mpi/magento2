<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CustomerBalance
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Customer balance total block for checkout
 *
 */
class Enterprise_CustomerBalance_Block_Checkout_Total extends Magento_Checkout_Block_Total_Default
{
    /**
     * @var string
     */
    protected $_template = 'Enterprise_CustomerBalance::checkout/total.phtml';
}
