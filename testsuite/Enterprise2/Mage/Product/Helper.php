<?php
# Magento
#
# {license_notice}
#
# @category    Magento
# @package     Mage_Product
# @subpackage  functional_tests
# @copyright   {copyright}
# @license     {license_link}
#
/**
 * Helper class
 */
class Enterprise2_Mage_Product_Helper extends Core_Mage_Product_Helper
{
    /**
     * Create Product without saving
     *
     * @param array $productData
     * @param string $productType
     */
    public function createProductWithoutSave(array $productData, $productType = 'simple')
    {
        $this->helper('Community2/Mage/Product')->createProductWithoutSave($productData, $productType);
    }

    /**
     * Import custom options from existent product
     *
     * @param mixed $productSku String or Array of SKUs
     */
    public function importCustomOptions($productSku)
    {
        $this->helper('Community2/Mage/Product')->importCustomOptions($productSku);
    }

    /**
     * Delete all custom options
     *
     * @return bool
     */
    public function deleteAllCustomOptions()
    {
        $this->helper('Community2/Mage/Product')->deleteAllCustomOptions();
    }

    /**
     * Verify Custom Options
     *
     * @param array $customOptionData
     *
     * @return boolean
     */
    public function verifyCustomOption(array $customOptionData)
    {
        $this->helper('Community2/Mage/Product')->verifyCustomOption($customOptionData);
    }
}
