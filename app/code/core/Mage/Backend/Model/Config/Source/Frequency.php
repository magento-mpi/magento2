<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Frequency
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'always', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Always')),
            array('value'=>'hourly', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Hourly')),
            array('value'=>'daily', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Daily')),
            array('value'=>'weekly', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Weekly')),
            array('value'=>'monthly', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Monthly')),
            array('value'=>'yearly', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Yearly')),
            array('value'=>'never', 'label'=>Mage::helper('Mage_Sitemap_Helper_Data')->__('Never')),
        );
    }
}
