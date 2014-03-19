<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Wishlist\Block\Customer\Wishlist;

class ItemsTest extends \PHPUnit_Framework_TestCase
{
    public function testGetColumns()
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $block = $layout->addBlock('Magento\Wishlist\Block\Customer\Wishlist\Items', 'test');
        $child = $this->getMock(
            'Magento\View\Element\Text',
            array('isEnabled'),
            array(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Element\Context'))
        );
        $child->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }
}
