<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Magento_Catalog_Model_Config_Source_ListMode implements Magento_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'grid', 'label'=>Mage::helper('Magento_Catalog_Helper_Data')->__('Grid Only')),
            array('value'=>'list', 'label'=>Mage::helper('Magento_Catalog_Helper_Data')->__('List Only')),
            array('value'=>'grid-list', 'label'=>Mage::helper('Magento_Catalog_Helper_Data')->__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>Mage::helper('Magento_Catalog_Helper_Data')->__('List (default) / Grid')),
        );
    }
}
