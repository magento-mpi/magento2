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

class Subtotal extends \Magento\Adminhtml\Block\Sales\Order\Create\Totals\DefaultTotals
{
    protected $_template = 'sales/order/create/totals/subtotal.phtml';

    /**
     * Check if we need display both sobtotals
     *
     * @return bool
     */
    public function displayBoth()
    {
        /**
         * Check without store parameter - we wil get admin configuration value
         */
        return \Mage::getSingleton('Magento\Tax\Model\Config')->displayCartSubtotalBoth();
    }
}
