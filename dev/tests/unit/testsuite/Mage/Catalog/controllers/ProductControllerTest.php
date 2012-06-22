<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Catalog/controllers/ProductController.php';

class Mage_Catalog_ProductControllerTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Controller_Product_View_Interface',
            $this->getMock('Mage_Catalog_ProductController', array(), array(), '', false)
        );
    }
}
