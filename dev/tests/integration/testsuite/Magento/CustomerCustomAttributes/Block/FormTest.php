<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CustomerCustomAttributes\Block;

/**
 * @magentoAppIsolation enabled
 */
class FormTest extends \PHPUnit_Framework_TestCase
{

    public function testPrepareLayout()
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout');
        $template = $layout->createBlock('Magento\Core\Block\Text', 'customer_form_template');
        $template->setData('renderers', array('test' => array(
            'block' => 'Magento\Core\Block\Text', 'template' => '1.phtml'
        )));
        $block = $layout->createBlock('Magento\CustomerCustomAttributes\Block\Form');
        $testRenderer = $block->getRenderer('test');
        $this->assertInstanceOf('Magento\Core\Block\Text', $testRenderer);
        $this->assertNotSame($template, $testRenderer);
        $this->assertEquals('1.phtml', $testRenderer->getTemplate());
    }

    public function testPrepareLayoutNoRenderer()
    {
        $layout = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()
            ->create('Magento\Core\Model\Layout');
        $layout->createBlock('Magento\Core\Block\Text', 'customer_form_template');
        $block = $layout->createBlock('Magento\CustomerCustomAttributes\Block\Form');
        $this->assertEquals('', $block->toHtml());
    }
}
