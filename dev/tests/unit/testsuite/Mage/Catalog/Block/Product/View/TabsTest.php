<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Catalog_Block_Product_View_TabsTest extends PHPUnit_Framework_TestCase
{
    public function testAddTab()
    {
        $tabBlock = $this->getMock('Mage_Core_Block_Template', array(), array(), '', false);
        $tabBlock->expects($this->once())
            ->method('setTemplate')
            ->with('template')
            ->will($this->returnSelf());

        $layout = $this->getMock('Mage_Core_Model_Layout', array(), array(), '', false);
        $layout->expects($this->once())
            ->method('createBlock')
            ->with('block')
            ->will($this->returnValue($tabBlock));

        $context = $this->getMock('Mage_Core_Block_Template_Context', array(), array(), '', false);
        $context->expects($this->once())
            ->method('getLayout')
            ->will($this->returnValue($layout));

        $block = new Mage_Catalog_Block_Product_View_Tabs($context);
        $result = $block->addTab('alias', 'title', 'block', 'template', 'header');

        $this->assertTrue($result);
        $expectedTabs = array(
            array('alias' => 'alias', 'title' => 'title', 'header' => 'header')
        );
        $this->assertEquals($expectedTabs, $block->getTabs());
    }
}
