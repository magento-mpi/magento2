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
 * Used in creating options for Yes|No|Specified config value selection
 *
 */
class Mage_Backend_Model_Config_Source_Yesnocustom implements Magento_Core_Model_Option_ArrayInterface
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
            array('value' => 2, 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Specified'))
        );
    }

}
