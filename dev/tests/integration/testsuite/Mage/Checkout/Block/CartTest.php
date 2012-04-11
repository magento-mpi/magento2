<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Checkout_Block_Cart
 */
class Mage_Checkout_Block_CartTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        $layout = new Mage_Core_Model_Layout;
        $child = $layout->createBlock('Mage_Core_Block_Text')
            ->setChild('child1', $layout->createBlock('Mage_Core_Block_Text', 'method1'))
            ->setChild('child2', $layout->createBlock('Mage_Core_Block_Text', 'method2'));
        $block = $layout->createBlock('Mage_Checkout_Block_Cart')
            ->setChild('child', $child);
        $methods = $block->getMethods('child');
        $this->assertEquals(array('method1', 'method2'), $methods);
    }

    public function testGetMethodsEmptyChild()
    {
        $layout = new Mage_Core_Model_Layout;
        $childEmpty = $layout->createBlock('Mage_Core_Block_Text');
        $block = $layout->createBlock('Mage_Checkout_Block_Cart')
            ->setChild('child', $childEmpty);
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }

    public function testGetMethodsNoChild()
    {
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->createBlock('Mage_Checkout_Block_Cart');
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }
}
