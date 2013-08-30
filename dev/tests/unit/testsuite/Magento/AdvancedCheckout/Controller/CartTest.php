<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_AdvancedCheckout
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_AdvancedCheckout_Controller_CartTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento_Catalog_Controller_Product_View_Interface',
            $this->getMock('Magento_AdvancedCheckout_Controller_Cart', array(), array(), '', false)
        );
    }
}
