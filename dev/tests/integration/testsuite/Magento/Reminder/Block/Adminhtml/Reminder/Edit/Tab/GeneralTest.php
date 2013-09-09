<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General
 * @magentoAppArea adminhtml
 */
class Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_GeneralTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        /** @var $objectManager Magento_Test_ObjectManager */
        $objectManager = Magento_Test_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')
            ->register('current_reminder_rule', Mage::getModel('Magento_Reminder_Model_Rule'));

        $block = Mage::app()->getLayout()->createBlock(
            'Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General'
        );
        $prepareFormMethod = new ReflectionMethod(
            'Magento_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('from_date', 'to_date') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
