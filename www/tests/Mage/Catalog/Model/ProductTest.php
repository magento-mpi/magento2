<?php

class Mage_Catalog_Model_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testLoad()
    {
        $product = Mage::getModel('catalog', 'product')->load(500);
        // Assert that the size of the Array fixture is 0.
        //$this->assertEquals(0, sizeof($fixture));
    }
}
