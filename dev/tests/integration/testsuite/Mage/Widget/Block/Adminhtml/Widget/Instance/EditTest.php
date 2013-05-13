<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Widget
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Widget_Block_Adminhtml_Widget_Instance_EditTest extends Mage_Backend_Area_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDataFixture Mage/Widget/_files/widget.php
     */
    public function testConstruct()
    {
        $widgetInstance = Mage::registry('current_widget_instance');
        Mage::app()->getRequest()->setParam('instance_id', $widgetInstance->getId());
        $block = Mage::app()->getLayout()->createBlock('Mage_Widget_Block_Adminhtml_Widget_Instance_Edit', 'widget');
        $this->assertArrayHasKey('widget-delete_button', $block->getLayout()->getAllBlocks());
    }
}
