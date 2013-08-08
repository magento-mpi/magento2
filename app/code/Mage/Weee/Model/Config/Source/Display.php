<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Weee_Model_Config_Source_Display implements Magento_Core_Model_Option_ArrayInterface
{
    /**
     * Retrieve list of available options to display FPT
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => Mage_Weee_Model_Tax::DISPLAY_INCL,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Including FPT only')
            ),
            array(
                'value' => Mage_Weee_Model_Tax::DISPLAY_INCL_DESCR,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Including FPT and FPT description')
            ),
            array(
                'value' => Mage_Weee_Model_Tax::DISPLAY_EXCL_DESCR_INCL,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Excluding FPT, FPT description, final price')
            ),
            array(
                'value' => Mage_Weee_Model_Tax::DISPLAY_EXCL,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Excluding FPT')
            ),
        );
    }

}
