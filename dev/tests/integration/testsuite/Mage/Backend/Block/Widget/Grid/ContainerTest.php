<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Mage_Backend_Block_Widget_Grid_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block Mage_Backend_Block_Widget_Grid_Container */
        $block = Mage::app()->getLayout()->createBlock('Mage_Backend_Block_Widget_Grid_Container', '', array(
            'data' => array(
                Mage_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'widget',
                Mage_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => 'two',
                Mage_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Mage_Backend',
                Mage_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => 'four',
                Mage_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_BACK => 'five',
            )
        ));
        $this->assertStringEndsWith('widget', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
        $this->assertInstanceOf('Mage_Backend_Block_Widget_Grid', $block->getChildBlock('grid'));
        $this->assertEquals('four', $block->getAddButtonLabel());
        $this->assertEquals('five', $block->getBackButtonLabel());
    }
}
