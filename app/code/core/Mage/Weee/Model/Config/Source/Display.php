<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Weee
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Mage_Weee_Model_Config_Source_Display
{

    public function toOptionArray()
    {
        /**
         * VAT is not applicable to FPT separately (we can't have FPT incl/excl VAT)
         */
        return array(
            array(
                'value' => 0,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Including FPT only')
            ),
            array(
                'value' => 1,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Including FPT and FPT description')
            ),
            //array('value'=>4, 'label'=>Mage::helper('Mage_Weee_Helper_Data')->__('Including FPT and FPT description [incl. FPT VAT]')),
            array(
                'value' => 2,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Excluding FPT, FPT description, final price')
            ),
            array(
                'value' => 3,
                'label' => Mage::helper('Mage_Weee_Helper_Data')->__('Excluding FPT')
            ),
        );
    }

}
