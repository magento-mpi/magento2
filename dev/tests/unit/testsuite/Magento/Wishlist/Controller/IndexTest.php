<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Wishlist_Controller_IndexTest extends PHPUnit_Framework_TestCase
{
    public function testControllerImplementsProductViewInterface()
    {
        $this->assertInstanceOf(
            '\Magento\Catalog\Controller\Product\View\ViewInterface',
            $this->getMock('Magento\Wishlist\Controller\Index', array(), array(), '', false)
        );
    }
}
