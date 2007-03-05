<?php



class Ecom_Catalog_Model_Mysql4_Attribute extends Ecom_Catalog_Model_Mysql4
{
    function getInputMethods()
    {
        return array(
            'input'=>'One-line text',
            'textarea'=>'Multi-line text',
            'richeditor'=>'Rich Editor',
            'calendar'=>'Calendar',
        );
    }
}