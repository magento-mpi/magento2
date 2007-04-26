<?php

class Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    protected $fixture;
 
    protected function setUp()
    {
        // Create a fixture.
        $this->fixture = Mage::getModel('catalog', 'product');
    }


    public function testLoad()
    {
        $product = $this->fixture->load(500);
        $this->assertEquals(500, $this->fixture->getId());
    }
    
    public function testSave()
    {
        
    }
}
