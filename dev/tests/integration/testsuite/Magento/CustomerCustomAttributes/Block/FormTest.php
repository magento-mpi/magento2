<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */
/**
 * @magentoAppIsolation enabled
 */
class Magento_CustomerCustomAttributes_Block_FormTest extends PHPUnit_Framework_TestCase
{

    public function testPrepareLayout()
    {
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $template = $layout->createBlock('Magento_Core_Block_Text', 'customer_form_template');
        $template->setData('renderers', array('test' => array(
            'block' => 'Magento_Core_Block_Text', 'template' => '1.phtml'
        )));
        $block = $layout->createBlock('Magento_CustomerCustomAttributes_Block_Form');
        $testRenderer = $block->getRenderer('test');
        $this->assertInstanceOf('Magento_Core_Block_Text', $testRenderer);
        $this->assertNotSame($template, $testRenderer);
        $this->assertEquals('1.phtml', $testRenderer->getTemplate());
    }

    public function testPrepareLayoutNoRenderer()
    {
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        $layout->createBlock('Magento_Core_Block_Text', 'customer_form_template');
        $block = $layout->createBlock('Magento_CustomerCustomAttributes_Block_Form');
        $this->assertEquals('', $block->toHtml());
    }
}
