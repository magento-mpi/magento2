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
 * Used in creating options for Yes|No config value selection
 *
 */
class Mage_Adminhtml_Model_System_Config_Source_Yesno
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes')),
            array('value' => 0, 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('No')),
        );
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        return array(
            0 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('No'),
            1 => Mage::helper('Mage_Adminhtml_Helper_Data')->__('Yes'),
        );
    }

}
