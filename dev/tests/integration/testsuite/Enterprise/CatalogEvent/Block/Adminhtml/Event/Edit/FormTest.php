<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_CatalogEvent
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form
 */
class Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_FormTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Core/_files/init_adminhtml_design.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        /** @var $event Enterprise_CatalogEvent_Model_Event */
        $event = Mage::getModel('Enterprise_CatalogEvent_Model_Event');
        $event->setCategoryId(1)->setId(1);
        Mage::register('enterprise_catalogevent_event', $event);
        $block = Mage::app()->getLayout()->createBlock('Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form');
        $prepareFormMethod = new ReflectionMethod(
            'Enterprise_CatalogEvent_Block_Adminhtml_Event_Edit_Form', '_prepareForm');
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
