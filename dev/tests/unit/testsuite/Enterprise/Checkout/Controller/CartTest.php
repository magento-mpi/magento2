<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Checkout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Checkout_Controller_CartTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento_Catalog_Controller_Product_View_Interface',
            $this->getMock('Enterprise_Checkout_Controller_Cart', array(), array(), '', false)
        );
    }
}
