<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_Wishlist
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Wishlist_Block_Customer_Wishlist_ItemsTest extends PHPUnit_Framework_TestCase
{
    public function testGetColumns()
    {
        $layout = new Mage_Core_Model_Layout;
        $block = $layout->addBlock('Mage_Wishlist_Block_Customer_Wishlist_Items', 'test');
        $child = $this->getMock('Mage_Core_Block_Text', array('isEnabled'));
        $child->expects($this->any())
            ->method('isEnabled')
            ->will($this->returnValue(true))
        ;
        $layout->addBlock($child, 'child', 'test');
        $this->assertSame(array($child), $block->getColumns());
    }
}
