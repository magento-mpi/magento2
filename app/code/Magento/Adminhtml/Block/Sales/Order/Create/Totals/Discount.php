<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Adminhtml\Block\Sales\Order\Create\Totals;

class Discount extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals
{
    //protected $_template = 'tax/checkout/subtotal.phtml';

    public function displayBoth()
    {
        return \Mage::getSingleton('Magento\Tax\Model\Config')->displayCartSubtotalBoth();
    }
}
