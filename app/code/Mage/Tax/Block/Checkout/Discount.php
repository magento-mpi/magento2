<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Subtotal Total Row Renderer
 *
 * @author Magento Core Team <core@magentocommerce.com>
 */

class Mage_Tax_Block_Checkout_Discount extends Magento_Checkout_Block_Total_Default
{
    public function displayBoth()
    {
        return Mage::getSingleton('Mage_Tax_Model_Config')->displayCartSubtotalBoth();
    }
}
