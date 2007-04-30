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
    
    public function testSave()
    {
        $data = array(
            //'category_id' => 13,
            'parent_id' => 14,
            'attribute_set_id'    => 1,
            'type_id'   => 1,
            'attributes'=> array(
                1 => 'Test save product',           // name
                2 => 'Test product desccription',   // description
                3 => '/media/images/imgmap.jpg',    // image
                5 => 'Title',                       // meta title
                6 => 'Keywords',                    // meta keywords
                7 => 'Description',                 // meta description
            ),
        );
        
        $category = Mage::getModel('catalog', 'category')->setData($data);
        $category->save();
    }
}

