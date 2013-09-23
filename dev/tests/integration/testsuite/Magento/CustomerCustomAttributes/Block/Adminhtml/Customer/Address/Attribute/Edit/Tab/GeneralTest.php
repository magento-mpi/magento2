<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CustomerCustomAttributes
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General
 * @magentoAppArea adminhtml
 */
class Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_GeneralTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $entityType = Magento_TestFramework_Helper_Bootstrap::getObjectManager()->get('Magento_Eav_Model_Config')
            ->getEntityType('customer');
        /** @var $model Magento_Customer_Model_Attribute */
        $model = Mage::getModel('Magento_Customer_Model_Attribute');
        $model->setEntityTypeId($entityType->getId());
        /** @var $objectManager Magento_TestFramework_ObjectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();
        $objectManager->get('Magento_Core_Model_Registry')->register('entity_attribute', $model);

        $block = Mage::app()->getLayout()->createBlock(
            'Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General'
        );
        $prepareFormMethod = new ReflectionMethod(
            'Magento_CustomerCustomAttributes_Block_Adminhtml_Customer_Address_Attribute_Edit_Tab_General',
            '_prepareForm');
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
