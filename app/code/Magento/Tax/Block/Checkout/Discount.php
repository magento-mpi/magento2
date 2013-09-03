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

class Magento_Tax_Block_Checkout_Discount extends Magento_Checkout_Block_Total_Default
{
    public function displayBoth()
    {
        return Mage::getSingleton('Magento_Tax_Model_Config')->displayCartSubtotalBoth();
    }
}
