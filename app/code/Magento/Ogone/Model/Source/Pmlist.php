<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Ogone
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Ogone template Action Dropdown source
 */
class Magento_Ogone_Model_Source_Pmlist
{
    /**
     * Prepare ogone payment block layout as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Ogone_Model_Api::PMLIST_HORISONTAL_LEFT, 'label' => Mage::helper('Magento_Ogone_Helper_Data')->__('Horizontally grouped logo with group name on left')),
            array('value' => Magento_Ogone_Model_Api::PMLIST_HORISONTAL, 'label' => Mage::helper('Magento_Ogone_Helper_Data')->__('Horizontally grouped logo with no group name')),
            array('value' => Magento_Ogone_Model_Api::PMLIST_VERTICAL, 'label' => Mage::helper('Magento_Ogone_Helper_Data')->__('Verical list')),
        );
    }
}
