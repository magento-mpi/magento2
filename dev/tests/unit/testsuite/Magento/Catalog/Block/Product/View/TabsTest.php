<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\View;

class TabsTest extends \PHPUnit_Framework_TestCase
{
    public function testAddTab()
    {
        $tabBlock = $this->getMock('Magento\View\Element\Template', array(), array(), '', false);
        $tabBlock->expects($this->once())->method('setTemplate')->with('template')->will($this->returnSelf());

        $layout = $this->getMock('Magento\View\Layout', array(), array(), '', false);
        $layout->expects($this->once())->method('createBlock')->with('block')->will($this->returnValue($tabBlock));

        $helper = new \Magento\TestFramework\Helper\ObjectManager($this);
        $block = $helper->getObject('Magento\Catalog\Block\Product\View\Tabs', array('layout' => $layout));
        $block->addTab('alias', 'title', 'block', 'template', 'header');

        $expectedTabs = array(array('alias' => 'alias', 'title' => 'title', 'header' => 'header'));
        $this->assertEquals($expectedTabs, $block->getTabs());
    }
}
