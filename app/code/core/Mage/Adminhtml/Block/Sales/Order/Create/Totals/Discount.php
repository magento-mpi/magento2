<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Mage_Adminhtml_Block_Sales_Order_Create_Totals_Discount extends Mage_Adminhtml_Block_Sales_Order_Create_Totals_Default
{
    //protected $_template = 'tax/checkout/subtotal.phtml';

    public function displayBoth()
    {
        return Mage::getSingleton('Mage_Tax_Model_Config')->displayCartSubtotalBoth();
    }
}
