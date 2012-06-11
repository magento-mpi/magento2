<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

require 'Mage/Checkout/controllers/CartController.php';

class Mage_Checkout_CartControllerTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Mage_Catalog_Controller_Product_View_Interface',
            $this->getMock('Mage_Checkout_CartController', array(), array(), '', false)
        );
    }
}
