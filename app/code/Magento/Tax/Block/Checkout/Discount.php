<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Tax\Block\Checkout;

class Discount extends \Magento\Checkout\Block\Total\DefaultTotal
{
    public function displayBoth()
    {
        return \Mage::getSingleton('Magento\Tax\Model\Config')->displayCartSubtotalBoth();
    }
}
