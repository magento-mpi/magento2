<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for \Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form
 * @magentoAppArea adminhtml
 */
class Magento_CatalogEvent_Block_Adminhtml_Event_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento\Core\Model\View\DesignInterface')
            ->setArea(\Magento\Core\Model\App\Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        /** @var $event Magento_CatalogEvent_Model_Event */
        $event = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->create('Magento_CatalogEvent_Model_Event');
        $event->setCategoryId(1)->setId(1);
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento\Core\Model\Registry')->register('magento_catalogevent_event', $event);
        $block = Mage::app()->getLayout()->createBlock('Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form');
        $prepareFormMethod = new ReflectionMethod(
            'Magento\CatalogEvent\Block\Adminhtml\Event\Edit\Form', '_prepareForm');
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
