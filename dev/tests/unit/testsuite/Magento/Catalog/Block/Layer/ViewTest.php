<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Layer;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClearUrl()
    {
        $childBlock = new \Magento\Framework\Object();

        $block = $this->getMock(
            'Magento\LayeredNavigation\Block\Navigation', ['getChildBlock'], [], '', false
        );
        $block->expects($this->atLeastOnce())
            ->method('getChildBlock')
            ->with('state')
            ->will($this->returnValue($childBlock));

        $expectedUrl = 'http://example.com/clear_all/12/';
        $this->assertNotEquals($expectedUrl, $block->getClearUrl());
        $childBlock->setClearUrl($expectedUrl);
        $this->assertEquals($expectedUrl, $block->getClearUrl());
    }
}
