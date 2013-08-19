<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


class Mage_Catalog_Model_Config_Source_ListMode implements Mage_Core_Model_Option_ArrayInterface
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'grid', 'label'=>__('Grid Only')),
            array('value'=>'list', 'label'=>__('List Only')),
            array('value'=>'grid-list', 'label'=>__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>__('List (default) / Grid')),
        );
    }
}
