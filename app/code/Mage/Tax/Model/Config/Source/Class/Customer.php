<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Tax
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Tax_Model_Config_Source_Class_Customer implements Mage_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve a list of customer tax classes
     *
     * @return array
     */
    public function toOptionArray()
    {
        $taxClasses = Mage::getModel('Mage_Tax_Model_Class_Source_Customer')->toOptionArray();
        array_unshift($taxClasses, array('value' => '0', 'label' => __('None')));
        return $taxClasses;
    }
}
