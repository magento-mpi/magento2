<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Reminder
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General.
 *
 * @group module:Enterprise_Reminder
 */
class Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_GeneralTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        Mage::register('current_reminder_rule', Mage::getModel('Enterprise_Reminder_Model_Rule'));

        $layout = new Mage_Core_Model_Layout;
        $block = $layout->addBlock('Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General');
        $prepareFormMethod = new ReflectionMethod(
            'Enterprise_Reminder_Block_Adminhtml_Reminder_Edit_Tab_General', '_prepareForm');
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
