<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Ogone\Block;

/**
 * Ogone payment information block
 */
class Info extends \Magento\Payment\Block\Info\Cc
{
    /**
     * @var string
     */
    protected $_template = 'info.phtml';
}
