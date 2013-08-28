<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Controller_ProductTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            'Magento_Catalog_Controller_Product_View_Interface',
            $this->getMock('Magento_Catalog_Controller_Product', array(), array(), '', false)
        );
    }
}
