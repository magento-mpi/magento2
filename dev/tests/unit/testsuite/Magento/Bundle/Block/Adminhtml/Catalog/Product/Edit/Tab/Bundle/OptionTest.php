<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_OptionTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetAddButtonId()
    {
        $button = new \Magento\Object;

        $itemsBlock = $this->getMock('Magento\Object', array('getChildBlock'));
        $itemsBlock->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('add_button')
            ->will($this->returnValue($button));

        $layout = $this->getMock('Magento\Object', array('getBlock'));
        $layout->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('admin.product.bundle.items')
            ->will($this->returnValue($itemsBlock));

        $block = $this->getMock('Magento\Bundle\Block\Adminhtml\Catalog\Product\Edit\Tab\Bundle\Option',
            array('getLayout'), array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->assertNotEquals(42, $block->getAddButtonId());
        $button->setId(42);
        $this->assertEquals(42, $block->getAddButtonId());
    }
}
