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

class Mage_Wishlist_Controller_IndexTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento_Catalog_Controller_Product_View_Interface',
            $this->getMock('Mage_Wishlist_Controller_Index', array(), array(), '', false)
        );
    }
}
