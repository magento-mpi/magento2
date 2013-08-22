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
 * Factory for Adminhtml VAT validation block
 *
 * @category   Magento
 * @package    Magento_Adminhtml
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Adminhtml_Block_Customer_System_Config_ValidatevatFactory
{
    /**
     * Create new VAT validator
     *
     * @return Magento_Adminhtml_Block_Customer_System_Config_Validatevat
     */
    public function createVatValidator()
    {
        return Mage::getBlockSingleton('Magento_Adminhtml_Block_Customer_System_Config_Validatevat');
    }
}
