<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Checkout_Block_Cart
 */
class Magento_Checkout_Block_CartTest extends PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $child = $layout->createBlock('Magento_Core_Block_Text')
            ->setChild('child1', $layout->createBlock('Magento_Core_Block_Text', 'method1'))
            ->setChild('child2', $layout->createBlock('Magento_Core_Block_Text', 'method2'));
        /** @var $block Magento_Checkout_Block_Cart */
        $block = $layout->createBlock('Magento_Checkout_Block_Cart')
            ->setChild('child', $child);
        $methods = $block->getMethods('child');
        $this->assertEquals(array('method1', 'method2'), $methods);
    }

    public function testGetMethodsEmptyChild()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        $childEmpty = $layout->createBlock('Magento_Core_Block_Text');
        /** @var $block Magento_Checkout_Block_Cart */
        $block = $layout->createBlock('Magento_Checkout_Block_Cart')
            ->setChild('child', $childEmpty);
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }

    public function testGetMethodsNoChild()
    {
        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getSingleton('Magento_Core_Model_Layout');
        /** @var $block Magento_Checkout_Block_Cart */
        $block = $layout->createBlock('Magento_Checkout_Block_Cart');
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }
}
