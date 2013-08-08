<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Used in creating options for Yes|No config value selection
 *
 */
class Mage_Backend_Model_Config_Source_Yesno implements Magento_Core_Model_Option_ArrayInterface
{

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => 1, 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Yes')),
            array('value' => 0, 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('No')),
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
            0 => Mage::helper('Mage_Backend_Helper_Data')->__('No'),
            1 => Mage::helper('Mage_Backend_Helper_Data')->__('Yes'),
        );
    }

}
