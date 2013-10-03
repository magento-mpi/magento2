<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone payment iformation block
 */
namespace Magento\Ogone\Block;

class Info extends \Magento\Payment\Block\Info\Cc
{
    protected $_template = 'info.phtml';
}
