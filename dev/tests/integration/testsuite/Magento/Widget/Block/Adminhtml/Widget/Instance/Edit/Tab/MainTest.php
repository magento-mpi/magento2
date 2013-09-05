<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    public function testPackageThemeElement()
    {
        Mage::register('current_widget_instance', new \Magento\Object());
        $block = Mage::app()->getLayout()->createBlock(
            'Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main');
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('theme_id');
        $this->assertInstanceOf('\Magento\Data\Form\Element\Select', $element);
        $this->assertTrue($element->getDisabled());
    }
}
