<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Wishlist
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Wishlist/controllers/IndexController.php';

class Mage_Wishlist_IndexControllerTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Controller_Product_View_Interface',
            $this->getMock('Mage_Wishlist_IndexController', array(), array(), '', false)
        );
    }
}
