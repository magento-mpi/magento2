<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Catalog_Model_Config_Source_ListMode
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>'grid', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Grid Only')),
            array('value'=>'list', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('List Only')),
            array('value'=>'grid-list', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>Mage::helper('Mage_Catalog_Helper_Data')->__('List (default) / Grid')),
        );
    }
}
