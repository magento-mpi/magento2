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
 * Test class for \Magento\Checkout\Block\Cart
 */
namespace Magento\Checkout\Block;

class CartTest extends \PHPUnit_Framework_TestCase
{
    public function testGetMethods()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $child = $layout->createBlock(
            'Magento\Framework\View\Element\Text'
        )->setChild(
            'child1',
            $layout->createBlock('Magento\Framework\View\Element\Text', 'method1')
        )->setChild(
            'child2',
            $layout->createBlock('Magento\Framework\View\Element\Text', 'method2')
        );
        /** @var $block \Magento\Checkout\Block\Cart */
        $block = $layout->createBlock('Magento\Checkout\Block\Cart')->setChild('child', $child);
        $methods = $block->getMethods('child');
        $this->assertEquals(array('method1', 'method2'), $methods);
    }

    public function testGetMethodsEmptyChild()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        $childEmpty = $layout->createBlock('Magento\Framework\View\Element\Text');
        /** @var $block \Magento\Checkout\Block\Cart */
        $block = $layout->createBlock('Magento\Checkout\Block\Cart')->setChild('child', $childEmpty);
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }

    public function testGetMethodsNoChild()
    {
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        /** @var $block \Magento\Checkout\Block\Cart */
        $block = $layout->createBlock('Magento\Checkout\Block\Cart');
        $methods = $block->getMethods('child');
        $this->assertEquals(array(), $methods);
    }
}
