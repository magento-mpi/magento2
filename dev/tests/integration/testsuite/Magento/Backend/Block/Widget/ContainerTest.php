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
class Magento_Backend_Block_Widget_ContainerTest extends PHPUnit_Framework_TestCase
{
    public function testPseudoConstruct()
    {
        /** @var $block Magento_Backend_Block_Widget_Container */
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_Backend_Block_Widget_Container', '',
                array('data' => array(
                    Magento_Backend_Block_Widget_Container::PARAM_CONTROLLER => 'one',
                    Magento_Backend_Block_Widget_Container::PARAM_HEADER_TEXT => 'two',
                ))
            );
        $this->assertStringEndsWith('one', $block->getHeaderCssClass());
        $this->assertContains('two', $block->getHeaderText());
    }
}
