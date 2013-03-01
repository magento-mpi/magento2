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
 * Factory for Adminhtml VAT validation block
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Mage_Adminhtml_Block_Customer_System_Config_ValidatevatFactory
{
    /**
     * Create new VAT validator
     *
     * @return Mage_Adminhtml_Block_Customer_System_Config_Validatevat
     */
    public function createVatValidator()
    {
        return Mage::getBlockSingleton('Mage_Adminhtml_Block_Customer_System_Config_Validatevat');
    }
}
