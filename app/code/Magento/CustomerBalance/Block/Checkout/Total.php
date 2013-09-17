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
class Magento_CustomerBalance_Block_Checkout_Total extends Magento_Checkout_Block_Total_Default
{
    /**
     * @var string
     */
    protected $_template = 'Magento_CustomerBalance::checkout/total.phtml';
}
