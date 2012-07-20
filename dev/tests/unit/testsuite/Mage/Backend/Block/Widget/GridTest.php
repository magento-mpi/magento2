<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Backend
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Backend_Model_Url
 */
class Mage_Backend_Block_Widget_GridTest extends PHPUnit_Framework_TestCase
{
    public function testAddGetClearRss()
    {
        /** @var $block Mage_Backend_Block_Widget_Grid */
        $block = $this->getMockBuilder('Mage_Backend_Block_Widget_Grid')
            ->disableOriginalConstructor()
            ->setMethods(array('_getRssUrl'))
            ->getMock();

        $block->expects($this->any())
            ->method('_getRssUrl')
            ->will($this->returnValue('some_url'));

        $this->assertFalse($block->getRssLists());

        $block->addRssList('some_url', 'some_label');
        $element = reset($block->getRssLists());
        $this->assertEquals('some_url', $element->getUrl());
        $this->assertEquals('some_label', $element->getLabel());

        $block->clearRss();
        $this->assertFalse($block->getRssLists());
    }
}
