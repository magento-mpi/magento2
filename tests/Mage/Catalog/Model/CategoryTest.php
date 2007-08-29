<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   default
 * @package    Tests_Mage
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


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

