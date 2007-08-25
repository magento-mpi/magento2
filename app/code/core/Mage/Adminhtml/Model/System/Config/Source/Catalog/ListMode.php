<?php

class Mage_Adminhtml_Model_System_Config_Source_Catalog_ListMode
{
    public function toOptionArray()
    {
        return array(
            array('value'=>'grid', 'label'=>__('Grid only')),
            array('value'=>'list', 'label'=>__('List only')),
            array('value'=>'grid-list', 'label'=>__('Grid (default) / List')),
            array('value'=>'list-grid', 'label'=>__('List (default) / Grid')),
        );
    }
}