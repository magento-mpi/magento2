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

class Mage_Adminhtml_Block_Widget_TabsTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testAddTab()
    {
        $widgetInstance = new Mage_Widget_Model_Widget_Instance;
        Mage::register('current_widget_instance', $widgetInstance);

        $layout = new Mage_Core_Model_Layout;
        $block = $layout->createBlock('Mage_Adminhtml_Block_Widget_Tabs', 'block');
        $layout->addBlock('Mage_Widget_Block_Adminhtml_Widget_Instance_Edit_Tab_Main', 'child_tab', 'block');
        $block->addTab('tab_id', 'child_tab');

        $this->assertEquals(array('tab_id'), $block->getTabsIds());
    }
}
