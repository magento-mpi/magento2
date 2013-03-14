<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Cms
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * CMS Hierarchy Navigation Menu source model for Display list mode
 *
 * @category   Enterprise
 * @package    Enterprise_Cms
 */
class Enterprise_Cms_Model_Source_Hierarchy_Menu_Listmode
{
    /**
     * Retrieve options array
     *
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            ''          => Mage::helper('Enterprise_Cms_Helper_Data')->__('Default'),
            '1'         => Mage::helper('Enterprise_Cms_Helper_Data')->__('Numbers (1, 2, 3, ...)'),
            'a'         => Mage::helper('Enterprise_Cms_Helper_Data')->__('Lower Alpha (a, b, c, ...)'),
            'A'         => Mage::helper('Enterprise_Cms_Helper_Data')->__('Upper Alpha (A, B, C, ...)'),
            'i'         => Mage::helper('Enterprise_Cms_Helper_Data')->__('Lower Roman (i, ii, iii, ...)'),
            'I'         => Mage::helper('Enterprise_Cms_Helper_Data')->__('Upper Roman (I, II, III, ...)'),
            'circle'    => Mage::helper('Enterprise_Cms_Helper_Data')->__('Circle'),
            'disc'      => Mage::helper('Enterprise_Cms_Helper_Data')->__('Disc'),
            'square'    => Mage::helper('Enterprise_Cms_Helper_Data')->__('Square'),
        );
    }
}
