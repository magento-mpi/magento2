<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_Customer
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General.
 *
 * @group module:Enterprise_Customer
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_GeneralTest
    extends PHPUnit_Framework_TestCase
{
    public function testPrepareForm()
    {
        $entityType = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('customer');
        $model = Mage::getModel('Mage_Customer_Model_Attribute')->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $block = new Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General;
        $prepareFormMethod = new ReflectionMethod(
            'Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General', '_prepareForm');
        $prepareFormMethod->setAccessible(true);
        $prepareFormMethod->invoke($block);

        $form = $block->getForm();
        foreach (array('date_range_min', 'date_range_max') as $id) {
            $element = $form->getElement($id);
            $this->assertNotNull($element);
            $this->assertNotEmpty($element->getDateFormat());
        }
    }
}
