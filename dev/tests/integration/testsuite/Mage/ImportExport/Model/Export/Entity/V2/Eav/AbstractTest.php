<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  integration_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test for abstract export model V2
 *
 * @group module:Mage_ImportExport
 */
class Mage_ImportExport_Model_Export_Entity_V2_Eav_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Skipped attribute codes
     *
     * @var array
     */
    protected static $_skippedAttrs = array('confirmation', 'lastname');
    /**
     * @var Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract
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
        parent::setUp();

        /** @var $customerAttrs Mage_Customer_Model_Resource_Attribute_Collection */
        $customerAttrs = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');

        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract', array(),
            '', false);
        $this->_model->expects($this->any())
            ->method('getEntityTypeCode')
            ->will($this->returnValue($this->_entityCode));
        $this->_model->expects($this->any())
            ->method('getAttributeCollection')
            ->will($this->returnValue($customerAttrs));
        $this->_model->__construct();
    }

    protected function tearDown()
    {
        unset($this->_model);
        parent::tearDown();
    }

    /**
     * Test for method getEntityTypeId()
     */
    public function testGetEntityTypeId()
    {
        $entityCode = 'customer';
        $entityId = Mage::getSingleton('Mage_Eav_Model_Config')
            ->getEntityType($entityCode)
            ->getEntityTypeId();

        $this->assertEquals($entityId, $this->_model->getEntityTypeId());
    }

    /**
     * Test for method _getExportAttrCodes()
     *
     * @covers Mage_ImportExport_Model_Export_Entity_V2_Eav_Abstract::_getExportAttrCodes
     */
    public function testGetExportAttrCodes()
    {
        $this->_checkReflectionAccessible();

        $this->_model->setParameters($this->_getSkippedAttributes());
        $exportAttrsMethod = new ReflectionMethod($this->_model, '_getExportAttrCodes');
        $exportAttrsMethod->setAccessible(true);
        $exportAttrs = $exportAttrsMethod->invoke($this->_model);
        foreach (self::$_skippedAttrs as $code) {
            $this->assertNotContains($code, $exportAttrs);
        }
    }

    /**
     * Test for method filterEntityCollection()
     *
     * @magentoDataFixture Mage/ImportExport/_files/customers.php
     */
    public function testFilterEntityCollection()
    {
        $createdAtDate = '2013-01-01';
        /**
         * Change created_at date of first customer for future filter test.
         */
        $customers = Mage::registry('_fixture/Mage_ImportExport_Customer_Collection');
        $customers[0]->setCreatedAt($createdAtDate);
        $customers[0]->save();
        /**
         * Change type of created_at attribute. In this case we have possibility to test date rage filter
         */
        /** @var $attrsCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attrsCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attrsCollection->addFieldToFilter('attribute_code', 'created_at');
        /** @var $createdAtAttr Mage_Customer_Model_Attribute */
        $createdAtAttr = $attrsCollection->getFirstItem();
        $createdAtAttr->setBackendType('datetime');
        $createdAtAttr->save();
        /**
         * Prepare filter.
         */
        $parameters = array(
            Mage_ImportExport_Model_Export::FILTER_ELEMENT_GROUP => array(
                'email' => 'example.com',
                'created_at' => array($createdAtDate, ''),
                'store_id' => Mage::app()->getStore()->getId()
            )
        );
        $this->_model->setParameters($parameters);
        /** @var $customers Mage_Customer_Model_Resource_Customer_Collection */
        $collection = $this->_model->filterEntityCollection(
            Mage::getResourceModel('Mage_Customer_Model_Resource_Customer_Collection')
        );

        $this->assertCount(1, $collection);
        $this->assertEquals($customers[0]->getId(), $collection->getFirstItem()->getId());
    }

    /**
     * Test for method getAttributeOptions()
     */
    public function testGetAttributeOptions()
    {
        /** @var $attrsCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attrsCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attrsCollection->addFieldToFilter('attribute_code', 'gender');
        $attribute = $attrsCollection->getFirstItem();

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
        /** @var $attrsCollection Mage_Customer_Model_Resource_Attribute_Collection */
        $attrsCollection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $attrsCollection->addFieldToFilter('attribute_code', array('in' => self::$_skippedAttrs));
        $skippedAttrs = array();
        /** @var $attribute  Mage_Customer_Model_Attribute */
        foreach ($attrsCollection as $attribute) {
            $skippedAttrs[$attribute->getAttributeCode()] = $attribute->getId();
        }

        return array(
            Mage_ImportExport_Model_Export::FILTER_ELEMENT_SKIP => $skippedAttrs
        );
    }

    /**
     * Check that method ReflectionMethod::setAccessible exists
     */
    protected function _checkReflectionAccessible()
    {
        if (!method_exists('ReflectionMethod', 'setAccessible')) {
            $this->markTestSkipped('Test requires ReflectionMethod::setAccessible (PHP 5 >= 5.3.2).');
        }
    }
}
