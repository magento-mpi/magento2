<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main
 *
 * @magentoAppArea adminhtml
 */
class Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        Mage::register('current_target_rule', Mage::getModel('Magento_TargetRule_Model_Rule'));

        $block = Mage::app()->getLayout()->createBlock(
            'Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main'
        );
        $prepareFormMethod = new ReflectionMethod(
            'Magento_TargetRule_Block_Adminhtml_Targetrule_Edit_Tab_Main', '_prepareForm');
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
