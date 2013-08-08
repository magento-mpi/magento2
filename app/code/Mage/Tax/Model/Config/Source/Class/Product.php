<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Tax_Model_Config_Source_Class_Product implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve list of products
     *
     * @return array
     */
    public function toOptionArray()
    {
        return Mage::getModel('Mage_Tax_Model_Class_Source_Product')->toOptionArray();
    }
}
