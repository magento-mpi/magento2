<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Backend\Block\Widget\Form;

/**
 * @magentoAppArea adminhtml
 */
class ContainerTest extends \PHPUnit_Framework_TestCase
{
    public function testGetFormHtml()
    {
        $objectManager = \Magento\TestFramework\Helper\Bootstrap::getObjectManager();
        /** @var $layout \Magento\Framework\View\Layout */
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->get(
            'Magento\Framework\View\LayoutInterface'
        );
        // Create block with blocking _prepateLayout(), which is used by block to instantly add 'form' child
        /** @var $block \Magento\Backend\Block\Widget\Form\Container */
        $block = $this->getMock(
            'Magento\Backend\Block\Widget\Form\Container',
            array('_prepareLayout'),
            array($objectManager->create('Magento\Backend\Block\Template\Context'))
        );

        $layout->addBlock($block, 'block');
        $form = $layout->addBlock('Magento\Framework\View\Element\Text', 'form', 'block');

        $expectedHtml = '<b>html</b>';
        $this->assertNotEquals($expectedHtml, $block->getFormHtml());
        $form->setText($expectedHtml);
        $this->assertEquals($expectedHtml, $block->getFormHtml());
    }
}
