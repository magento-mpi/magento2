<?php
/**
 * Magento
 *
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Product
 * @subpackage  functional_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * Helper class
 */
class Enterprise2_Mage_Product_Helper extends Community2_Mage_Product_Helper
{
    /**
     * Import custom options from existent product
     *
     * @param mixed $productSku String or Array of SKUs
     */
    public function importCustomOptions($productSku)
    {
        parent::importCustomOptions($productSku);
    }

    /**
     * Delete all custom options
     *
     * @return bool
     */
    public function deleteAllCustomOptions()
    {
        parent::deleteAllCustomOptions();
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
        parent::verifyCustomOption($customOptionData);
    }
}
