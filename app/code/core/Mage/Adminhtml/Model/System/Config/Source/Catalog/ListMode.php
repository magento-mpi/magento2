<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Adminhtml
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode
{
    public function toOptionArray()
    {
        return array(
            //array('value'=>'', 'label'=>''),
            array('value'=>'grid', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Grid Only')),
            array('value'=>'list', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('List Only')),
            array('value'=>'grid-list', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>Mage::helper('Mage_Adminhtml_Helper_Data')->__('List (default) / Grid')),
        );
    }
}
