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
class Magento_Ogone_Model_Source_Template
{
    /**
     * Prepare ogone template mode list as option array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array('value' => Magento_Ogone_Model_Api::TEMPLATE_OGONE, 'label' => Mage::helper('Magento_Ogone_Helper_Data')->__('Ogone')),
            array('value' => Magento_Ogone_Model_Api::TEMPLATE_MAGENTO, 'label' => Mage::helper('Magento_Ogone_Helper_Data')->__('Magento')),
        );
    }
}
