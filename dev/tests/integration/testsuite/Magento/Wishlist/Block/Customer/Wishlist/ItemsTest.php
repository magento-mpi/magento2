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
        $layout = \Mage::getSingleton('Magento\Core\Model\Layout');
        $block = $layout->addBlock('Magento\Wishlist\Block\Customer\Wishlist\Items', 'test');
        $child = $this->getMock('Magento\Core\Block\Text', array('isEnabled'),
            array(\Mage::getSingleton('Magento\Core\Block\Context')));
        $child->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }
}
