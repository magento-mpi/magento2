<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Backend
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Backend_Model_Config_Source_Catalog_ListMode
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>'grid', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Grid Only')),
            array('value'=>'list', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('List Only')),
            array('value'=>'grid-list', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>Mage::helper('Mage_Backend_Helper_Data')->__('List (default) / Grid')),
        );
    }
}
