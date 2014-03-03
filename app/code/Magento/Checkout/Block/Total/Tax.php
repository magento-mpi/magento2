<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Checkout\Block\Total;

/**
 * Tax Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */
class Tax extends \Magento\Checkout\Block\Total\DefaultTotal
{
    /**
     * @var string
     */
    protected $_template = 'total/tax.phtml';
}
