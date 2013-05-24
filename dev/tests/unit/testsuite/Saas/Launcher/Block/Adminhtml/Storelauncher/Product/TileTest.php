<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Saas_Launcher_Block_Adminhtml_Storelauncher_Product_TileTest extends PHPUnit_Framework_TestCase
{
    public function testIsAddProductRestricted()
    {
        $expected = true;
        $context = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $limitation = $this->getMock('Mage_Catalog_Model_Product_Limitation', array(), array(), '', false);
        $limitation->expects($this->once())
            ->method('isCreateRestricted')
            ->will($this->returnValue($expected));
        $block = new Saas_Launcher_Block_Adminhtml_Storelauncher_Product_Tile($context, $limitation);
        $this->assertSame($expected, $block->isAddProductRestricted());
    }
}
