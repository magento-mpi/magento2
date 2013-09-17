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
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('current_widget_instance', new Magento_Object());
        $block = Mage::app()->getLayout()->createBlock(
            'Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main');
        $block->setTemplate(null);
        $block->toHtml();
        $element = $block->getForm()->getElement('theme_id');
        $this->assertInstanceOf('Magento_Data_Form_Element_Select', $element);
        $this->assertTrue($element->getDisabled());
    }
}
