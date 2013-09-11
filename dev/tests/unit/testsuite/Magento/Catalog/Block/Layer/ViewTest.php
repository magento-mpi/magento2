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

class Magento_Catalog_Block_Layer_ViewTest extends PHPUnit_Framework_TestCase
{
    public function testGetClearUrl()
    {
        $childBlock = new \Magento\Object;

        $block = $this->getMock('Magento\Catalog\Block\Layer\View', array('getChildBlock'), array(), '', false);
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('layer_state')
            ->will($this->returnValue($childBlock));

        $expectedUrl = 'http://example.com/clear_all/12/';
        $this->assertNotEquals($expectedUrl, $block->getClearUrl());
        $childBlock->setClearUrl($expectedUrl);
        $this->assertEquals($expectedUrl, $block->getClearUrl());
    }
}
