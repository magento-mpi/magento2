<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Bundle
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_OptionTest
    extends PHPUnit_Framework_TestCase
{
    public function testGetAddButtonId()
    {
        $button = new Varien_Object;

        $itemsBlock = $this->getMock('Varien_Object', array('getChildBlock'));
        $itemsBlock->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('add_button')
            ->will($this->returnValue($button));

        $layout = $this->getMock('Varien_Object', array('getBlock'));
        $layout->expects($this->atLeastOnce())
            ->method('getBlock')
            ->with('admin.product.bundle.items')
            ->will($this->returnValue($itemsBlock));

        $block = $this->getMock('Mage_Bundle_Block_Adminhtml_Catalog_Product_Edit_Tab_Bundle_Option',
            array('getLayout'), array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $this->assertNotEquals(42, $block->getAddButtonId());
        $button->setId(42);
        $this->assertEquals(42, $block->getAddButtonId());
    }
}
