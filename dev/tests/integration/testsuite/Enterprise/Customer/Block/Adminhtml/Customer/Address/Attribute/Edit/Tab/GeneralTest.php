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
 * Test class for Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General
 * @magentoAppArea adminhtml
 */
class Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_GeneralTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Mage::getDesign()->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)->setDefaultDesignTheme();
        $entityType = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('customer');
        /** @var $model Magento_Customer_Model_Attribute */
        $model = Mage::getModel('Magento_Customer_Model_Attribute');
        $model->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $block = Mage::app()->getLayout()->createBlock(
            'Enterprise_Customer_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General'
        );
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
