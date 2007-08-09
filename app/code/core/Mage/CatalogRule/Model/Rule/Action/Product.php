<?php

class Mage_Catalog_Model_Product_Rule_Action_Product extends Mage_Rule_Model_Action_Abstract 
{
    public function getOperatorOptions()
    {
        return array(
            'to_fixed'=>'TO FIXED value',
            'to_percent'=>'TO PERCENTAGE',
            'by_fixed'=>'BY FIXED value',
            'by_percent'=>'BY PERCENTAGE',
        );
    }
}