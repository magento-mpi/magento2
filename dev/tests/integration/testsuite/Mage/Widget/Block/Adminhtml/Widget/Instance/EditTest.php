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

/**
 * @magentoAppArea adminhtml
 */
class Mage_Widget_Block_Adminhtml_Widget_Instance_EditTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     * @magentoDbIsolation enabled
     */
    public function testConstruct()
    {
        $type = 'Mage_Catalog_Block_Product_Widget_New';
        $theme = Mage::getDesign()->setDefaultDesignTheme()->getDesignTheme();

        /** @var $widgetInstance Mage_Widget_Model_Widget_Instance */
        $widgetInstance = Mage::getModel('Mage_Widget_Model_Widget_Instance');
        $widgetInstance
            ->setType($type)
            ->setThemeId($theme->getId())
            ->save();
        Mage::register('current_widget_instance', $widgetInstance);

        Mage::app()->getRequest()->setParam('instance_id', $widgetInstance->getId());
        $block = Mage::app()->getLayout()->createBlock('Mage_Widget_Block_Adminhtml_Widget_Instance_Edit', 'widget');
        $this->assertArrayHasKey('widget-delete_button', $block->getLayout()->getAllBlocks());
    }
}
