<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for eav abstract export model
 */
class Magento_ImportExport_Model_Export_Entity_EavAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Skipped attribute codes
     *
     * @var array
     */
    protected static $_skippedAttributes = array('confirmation', 'lastname');

    /**
     * @var Magento_ImportExport_Model_Export_Entity_EavAbstract
     */
    protected $_model;

    /**
     * Entity code
     *
     * @var string
     */
    protected $_entityCode = 'customer';

    protected function setUp()
    {
        /** @var Magento_TestFramework_ObjectManager  $objectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $customerAttributes = Mage::getResourceModel('Magento_Customer_Model_Resource_Attribute_Collection');

        $storeConfig = $objectManager->get('Magento_Core_Model_Store_Config');
        $this->_model = $this->getMockForAbstractClass('Magento_ImportExport_Model_Export_Entity_EavAbstract',
            array($storeConfig), '', false);
        $this->_model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue($this->_entityCode));
        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue($customerAttributes));
        $this->_model->__construct($storeConfig);
    }

    /**
     * Test for method getEntityTypeId()
     */
    public function testGetEntityTypeId()
    {
        $entityCode = 'customer';
        $entityId = Mage::getSingleton('Magento_Eav_Model_Config')
            ->getEntityType($entityCode)
            ->getEntityTypeId();

        $this->assertEquals($entityId, $this->_model->getEntityTypeId());
    }

    /**
     * Test for method _getExportAttrCodes()
     *
     * @covers Magento_ImportExport_Model_Export_Entity_EavAbstract::_getExportAttrCodes
     */
    public function testGetExportAttrCodes()
    {
        $this->_model->setParameters($this->_getSkippedAttributes());
        $method = new ReflectionMethod($this->_model, '_getExportAttributeCodes');
        $method->setAccessible(true);
        $attributes = $method->invoke($this->_model);
        foreach (self::$_skippedAttributes as $code) {
            $this->assertNotContains($code, $attributes);
        }
    }

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        /** @var $attributeCollection Magento_Customer_Model_Resource_Attribute_Collection */
        $attributeCollection = Mage::getResourceModel('Magento_Customer_Model_Resource_Attribute_Collection');
        $attributeCollection->addFieldToFilter('attribute_code', 'gender');
        /** @var $attribute Magento_Customer_Model_Attribute */
        $attribute = $attributeCollection->getFirstItem();

        $expectedOptions = array();
        foreach ($attribute->getSource()->getAllOptions(false) as $option) {
            $expectedOptions[$option['value']] = $option['label'];
        }

        $actualOptions = $this->_model->getAttributeOptions($attribute);
        $this->assertEquals($expectedOptions, $actualOptions);
    }

    /**
     * Retrieve list of skipped attributes
     *
     * @return array
     */
    protected function _getSkippedAttributes()
    {
        /** @var $attributeCollection Magento_Customer_Model_Resource_Attribute_Collection */
        $attributeCollection = Mage::getResourceModel('Magento_Customer_Model_Resource_Attribute_Collection');
        $attributeCollection->addFieldToFilter('attribute_code', array('in' => self::$_skippedAttributes));
        $skippedAttributes = array();
        /** @var $attribute  Magento_Customer_Model_Attribute */
        foreach ($attributeCollection as $attribute) {
            $skippedAttributes[$attribute->getAttributeCode()] = $attribute->getId();
        }

        return array(
            Magento_ImportExport_Model_Export::FILTER_ELEMENT_SKIP => $skippedAttributes
        );
    }
}
