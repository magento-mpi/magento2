<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Eav
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
 */
class Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        Magento_Test_Helper_Bootstrap::getObjectManager()->get('Magento_Core_Model_View_DesignInterface')
            ->setArea(Magento_Core_Model_App_Area::AREA_ADMINHTML)
            ->setDefaultDesignTheme();
        $entityType = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType('customer');
        $model = Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Customer_Model_Attribute');
        $model->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $block = $this->getMockForAbstractClass(
            'Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract',
            array(Mage::getSingleton('Magento_Backend_Block_Template_Context'))
        )
        ->setLayout(Magento_Test_Helper_Bootstrap::getObjectManager()->create('Magento_Core_Model_Layout'));

        $method = new ReflectionMethod(
            'Magento_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract', '_prepareForm');
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
