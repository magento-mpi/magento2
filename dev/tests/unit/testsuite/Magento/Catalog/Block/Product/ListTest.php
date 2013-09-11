<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Magento_Catalog_Block_Product_ListTest extends PHPUnit_Framework_TestCase
{
    public function testGetMode()
    {
        $childBlock = new \Magento\Object;

        $block = $this->getMock('Magento\Catalog\Block\Product\ListProduct', array('getChildBlock'), array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('toolbar')
            ->will($this->returnValue($childBlock));

        $expectedMode = 'a mode';
        $this->assertNotEquals($expectedMode, $block->getMode());
        $childBlock->setCurrentMode($expectedMode);
        $this->assertEquals($expectedMode, $block->getMode());
    }
}
