<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_VersionsCms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Navigation Menu source model for Display list mode
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Menu_Listmode
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            ''          => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Default'),
            '1'         => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Numbers (1, 2, 3, ...)'),
            'a'         => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Lower Alpha (a, b, c, ...)'),
            'A'         => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Upper Alpha (A, B, C, ...)'),
            'i'         => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Lower Roman (i, ii, iii, ...)'),
            'I'         => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Upper Roman (I, II, III, ...)'),
            'circle'    => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Circle'),
            'disc'      => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Disc'),
            'square'    => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Square'),
        );
    }
}
