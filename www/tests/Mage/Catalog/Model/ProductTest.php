<?php

class Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    protected $product;
 
    protected function setUp()
    {
        // Create a fixture.
        $this->product = Mage::getModel('catalog', 'product');
    }


    public function testLoad()
    {
        $product = $this->product->load(500);
        $this->assertEquals(500, $this->product->getId());
    }
    
    public function testSave()
    {
        $attributes = array();
        $product = array();
        $super = array();
        
        
    }
}
