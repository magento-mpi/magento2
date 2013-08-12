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
class Magento_Adminhtml_Block_Sales_Order_Create_Totals_Subtotal extends Magento_Adminhtml_Block_Sales_Order_Create_Totals_Default
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
        return Mage::getSingleton('Magento_Tax_Model_Config')->displayCartSubtotalBoth();
    }
}
