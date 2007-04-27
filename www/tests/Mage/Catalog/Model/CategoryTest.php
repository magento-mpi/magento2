<?php

class Mage_Catalog_Model_CategoryTest extends PHPUnit_Framework_TestCase
{
    protected function setUp()
    {
    }

    public function testTree()
    {
        $tree = Mage::getModel('catalog_resource', 'category_tree');
        $tree->load();
    }
}

