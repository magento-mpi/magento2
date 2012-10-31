<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract
 */
class Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /**
     * @magentoAppIsolation enabled
     */
    public function testPrepareForm()
    {
        $entityType = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType('customer');
        $model = Mage::getObjectManager()->create('Mage_Customer_Model_Attribute');
        $model->setEntityTypeId($entityType->getId());
        Mage::register('entity_attribute', $model);

        $arguments = array(
            Mage::getObjectManager()->get('Mage_Core_Controller_Request_Http'),
            Mage::getObjectManager()->get('Mage_Core_Model_Layout'),
            Mage::getObjectManager()->get('Mage_Core_Model_Event_Manager'),
            Mage::getObjectManager()->get('Mage_Core_Model_Translate'),
            Mage::getObjectManager()->get('Mage_Core_Model_Cache'),
            Mage::getObjectManager()->get('Mage_Core_Model_Design_Package'),
            Mage::getObjectManager()->get('Mage_Core_Model_Session'),
            Mage::getObjectManager()->get('Mage_Core_Model_Store_Config'),
            Mage::getObjectManager()->get('Mage_Core_Controller_Varien_Front')
        );
        $block = $this->getMockForAbstractClass('Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract', $arguments)
            ->setLayout(Mage::getObjectManager()->create('Mage_Core_Model_Layout'));

        $method = new ReflectionMethod(
            'Mage_Eav_Block_Adminhtml_Attribute_Edit_Main_Abstract', '_prepareForm');
        $method->setAccessible(true);
        $method->invoke($block);

        $element = $block->getForm()->getElement('default_value_date');
        $this->assertNotNull($element);
        $this->assertNotEmpty($element->getDateFormat());
    }
}
