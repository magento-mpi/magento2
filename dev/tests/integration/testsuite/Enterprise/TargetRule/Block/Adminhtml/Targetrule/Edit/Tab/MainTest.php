<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main.
 *
 * @group module:Enterprise_TargetRule
 */
class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        Mage::register('current_target_rule', Mage::getModel('Enterprise_TargetRule_Model_Rule'));

        $layout = new Mage_Core_Model_Layout;
        $block = $layout->addBlock('Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main');
        $prepareFormMethod = new ReflectionMethod(
            'Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main', '_prepareForm');
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
