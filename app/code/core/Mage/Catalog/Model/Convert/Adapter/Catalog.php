<?php

class Mage_Catalog_Model_Convert_Adapter_Catalog extends Varien_Convert_Adapter_Abstract
{
    public function load()
    {
        $this->setData(array(
            'Worksheet1'=>array(
                array('field1'=>'value1'), array('field2'=>'value2'),
            ),
        ));
        return $this;
    }
    
    public function save()
    {
        
    }
}