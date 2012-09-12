<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Adminhtml
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main.
 *
 * @group module:Mage_Adminhtml
 */
class Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_MainTest extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        Mage::register('current_promo_quote_rule', Mage::getModel('Mage_SalesRule_Model_Rule'));

        $layout = new Mage_Core_Model_Layout;
        $block = $layout->addBlock('Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main');
        $prepareFormMethod = new ReflectionMethod(
            'Mage_Adminhtml_Block_Promo_Quote_Edit_Tab_Main', '_prepareForm');
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
