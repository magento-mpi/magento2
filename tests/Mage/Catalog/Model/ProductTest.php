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
        $data = array(
            'product_id' => 501,
            'set_id'    => 1,
            'type_id'   => 1,
            'attributes'=> array(
                1 => 'Test save product',           // name
                2 => 'Test product desccription',   // description
                3 => '/media/images/imgmap.jpg',    // image
                4 => 'TST MDL',                     // model
                5 => 11.33,                         // price
                6 => 12.42,                         // cost
                7 => '1',                            // add_date
                8 => 5,                             // weight
                9 => 1,                             // status
                10=> 7,                             // Manufacturer
                11=> 4,                             // Type
                12=> 5,                             // Category id
            ),
        );
        
        $product = Mage::getModel('catalog', 'product')->setData($data);
        $product->save();
        
        $product = Mage::getModel('catalog', 'product')->load(501);
        //var_dump($product->getTierPrice());
    }
}
