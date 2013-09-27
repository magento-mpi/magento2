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
class Magento_Backend_Block_Widget_Grid_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block Magento_Backend_Block_Widget_Grid_Container */
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Backend_Block_Widget_Grid_Container', '', array(
                'data' => array(
                    Magento_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'widget',
                    Magento_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => 'two',
                    Magento_Backend_Block_Widget_Grid_Container::PARAM_BLOCK_GROUP => 'Magento_Backend',
                    Magento_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_NEW => 'four',
                    Magento_Backend_Block_Widget_Grid_Container::PARAM_BUTTON_BACK => 'five',
                )
            ));
        $this->assertStringEndsWith('widget', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
        $this->assertInstanceOf('Magento_Backend_Block_Widget_Grid', $block->getChildBlock('grid'));
        $this->assertEquals('four', $block->getAddButtonLabel());
        $this->assertEquals('five', $block->getBackButtonLabel());
    }
}
