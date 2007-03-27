<?php

class Mage_Cart_Model_Mysql4_Cart extends Mage_Cart_Model_Mysql4
{

    function getProducts($cartId=null)
    {
        $arr = array(
            array('id'=>1, 'qty'=>2, 'name'=>'Test Product', 'price'=>12.34),
        );
        #$arr = array();
        
        return $arr;
    }
    
    function addProduct($cartId=null)
    {
        
        
    }
    
    function update($cartData, $cartId=null)
    {
        
    }
}