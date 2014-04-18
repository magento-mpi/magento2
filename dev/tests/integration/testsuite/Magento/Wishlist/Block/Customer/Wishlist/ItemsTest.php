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
        ini_set('memory_limit', '1024M');
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\LayoutInterface');
        $block = $layout->addBlock('Magento\Wishlist\Block\Customer\Wishlist\Items', 'test');
        $child = $this->getMock(
            'Magento\Wishlist\Block\Customer\Wishlist\Item\Column',
            array('isEnabled'),
            array(\Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get('Magento\View\Element\Context')),
            '',
            false
        );
        $child->expects($this->any())->method('isEnabled')->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $expected = $child->getType();
        $columns = $block->getColumns();
        $this->assertNotEmpty($columns);
        foreach ($columns as $column) {
            $this->assertSame($expected, $column->getType());
        }
    }
}
