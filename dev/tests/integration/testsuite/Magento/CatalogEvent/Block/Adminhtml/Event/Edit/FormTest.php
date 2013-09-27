<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_CatalogEvent_Block_Adminhtml_Event_Edit_Form
 * @magentoAppArea adminhtml
 */
class Magento_CatalogEvent_Block_Adminhtml_Event_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        /** @var $event Magento_CatalogEvent_Model_Event */
        $event = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_CatalogEvent_Model_Event');
        $event->setCategoryId(1)->setId(1);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('magento_catalogevent_event', $event);
        $block = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_Layout')
            ->createBlock('Magento_CatalogEvent_Block_Adminhtml_Event_Edit_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Magento_CatalogEvent_Block_Adminhtml_Event_Edit_Form', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_start', 'date_end') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
            $this->assertNotEmpty($element->getTimeFormat());
        }
    }
}
