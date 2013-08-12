<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * @magentoAppArea adminhtml
 */
class Magento_Adminhtml_Block_Widget_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        /** @var $widgetInstance Magento_Widget_Model_Widget_Instance */
        $widgetInstance = Mage::getModel('Magento_Widget_Model_Widget_Instance');
        Mage::register('current_widget_instance', $widgetInstance);

        /** @var $layout Magento_Core_Model_Layout */
        $layout = Mage::getModel('Magento_Core_Model_Layout');
        /** @var $block Magento_Adminhtml_Block_Widget_Tabs */
        $block = $layout->createBlock('Magento_Adminhtml_Block_Widget_Tabs', 'block');
        $layout->addBlock('Magento_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}
