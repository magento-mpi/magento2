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
 * Test class for Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main
 */
class Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoDataFixture Mage/Core/_files/init_adminhtml_design.php
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::register('current_target_rule', Mage::getModel('Enterprise_TargetRule_Model_Rule'));

        $block = Mage::app()->getLayout()->createBlock(
            'Enterprise_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main'
        );
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
