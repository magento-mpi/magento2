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
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $block = $layout->addBlock('Magento_Wishlist_Block_Customer_Wishlist_Items', 'test');
        $child = $this->getMock('Magento_Core_Block_Text', array('isEnabled'),
            array(Mage::getSingleton('Magento_Core_Block_Context')));
        $child->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true));
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }
}
