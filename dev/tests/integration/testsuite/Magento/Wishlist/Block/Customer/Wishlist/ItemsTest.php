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

class Magento_Wishlist_Block_Customer_Wishlist_ItemsTest extends PHPUnit_Framework_TestCase
{
    public function testGetColumns()
    {
        $layout = Mage::getModel('Magento\Core\Model\Layout');
        $block = $layout->addBlock('\Magento\Wishlist\Block\Customer\Wishlist\Items', 'test');
        $child = $this->getMock('Magento\Core\Block\Text', array('isEnabled'),
            array(Mage::getSingleton('Magento\Core\Block\Context')));
        $child->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }
}
