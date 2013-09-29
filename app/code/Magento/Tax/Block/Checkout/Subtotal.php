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

class Subtotal extends \Magento\Checkout\Block\Total\DefaultTotal
{
    protected $_template = 'checkout/subtotal.phtml';

    public function displayBoth()
    {
        return \Mage::getSingleton('Magento\Tax\Model\Config')->displayCartSubtotalBoth($this->getStore());
    }
}
