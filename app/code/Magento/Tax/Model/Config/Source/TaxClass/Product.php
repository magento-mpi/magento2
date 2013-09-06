<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Tax_Model_Config_Source_TaxClass_Product implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getModel('Magento_Tax_Model_TaxClass_Source_Product')->toOptionArray();
    }
}
