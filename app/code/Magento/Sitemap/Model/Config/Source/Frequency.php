<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Sitemap_Model_Config_Source_Frequency implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'always', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Always')),
            array('value'=>'hourly', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Hourly')),
            array('value'=>'daily', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Daily')),
            array('value'=>'weekly', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Weekly')),
            array('value'=>'monthly', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Monthly')),
            array('value'=>'yearly', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Yearly')),
            array('value'=>'never', 'label'=>Mage::helper('Magento_Sitemap_Helper_Data')->__('Never')),
        );
    }
}
