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
 * CMS Hierarchy Menu source model for Chapter/Section options
 *
 * @category   Magento
 * @package    Magento_VersionsCms
 */
class Magento_VersionsCms_Model_Source_Hierarchy_Menu_Chapter
{
    /**
     * Return options for Chapter/Section meta links
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = array(
            array('label' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('No'), 'value' => ''),
            array('label' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Chapter'), 'value' => 'chapter'),
            array('label' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Section'), 'value' => 'section'),
            array('label' => Mage::helper('Magento_VersionsCms_Helper_Data')->__('Both'), 'value' => 'both'),
        );

        return $options;
    }
}
