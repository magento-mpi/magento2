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
            'Magento\Catalog\Controller\Product\View\ViewInterface',
            $this->getMock('Magento\AdvancedCheckout\Controller\Cart', array(), array(), '', false)
        );
    }
}
