<?php

class Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $product = Mage::getModel('catalog', 'product')->load(500);
        $this->assertEquals(500, $product->getId());
    }
    
    public function testSave()
    {
        
    }
}
