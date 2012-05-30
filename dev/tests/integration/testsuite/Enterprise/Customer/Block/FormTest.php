<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_Customer_Block_FormTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareLayout()
    {
        $layout = new Mage_Core_Model_Layout;
        $template = $layout->createBlock('Mage_Core_Block_Text', 'customer_form_template');
        $template->setData('renderers', array('test' => array('block' => 'Mage_Core_Block_Text', 'template' => '1')));
        $block = $layout->createBlock('Enterprise_Customer_Block_Form');
        $testRenderer = $block->getRenderer('test');
        $this->assertInstanceOf('Mage_Core_Block_Text', $testRenderer);
        $this->assertNotSame($template, $testRenderer);
    }
}
