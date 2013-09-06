<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Tax_Model_Config_Source_TaxClass_Customer implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve a list of customer tax classes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $taxClasses = Mage::getModel('Magento_Tax_Model_TaxClass_Source_Customer')->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
