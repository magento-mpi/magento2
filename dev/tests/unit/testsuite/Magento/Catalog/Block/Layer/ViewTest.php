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
namespace Magento\Catalog\Block\Layer;

class ViewTest extends \PHPUnit_Framework_TestCase
{
    public function testGetClearUrl()
    {
        $childBlock = new \Magento\Object();

        $block = $this->getMock(
            'Magento\LayeredNavigation\Block\Navigation', array('getChildBlock'), array(), '', false
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
