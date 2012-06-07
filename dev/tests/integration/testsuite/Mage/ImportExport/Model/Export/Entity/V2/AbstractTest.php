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
class Mage_ImportExport_Model_Export_Entity_V2_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Export_Entity_V2_Abstract
     */
    protected $_model;


    protected function setUp()
    {
        parent::setUp();
        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_Entity_V2_Abstract');
    }

    protected function tearDown()
    {
        unset($this->_model);
        parent::tearDown();
    }

    /**
     * Check methods which provide ability to manage errors
     */
    public function testAddRowError()
    {
        $errorCode = 'test_error';
        $errorNum = 1;
        $errorMessage = 'Test error!';
        $this->_model->addMessageTemplate($errorCode, $errorMessage);
        $this->_model->addRowError($errorCode, $errorNum);

        $this->assertEquals(1, $this->_model->getErrorsCount());
        $this->assertEquals(1, $this->_model->getInvalidRowsCount());
        $this->assertArrayHasKey($errorMessage, $this->_model->getErrorMessages());
    }

    /**
     * Check methods which provide ability to manage writer object
     *
     * @expectedException Mage_Core_Exception
     */
    public function testGetWriter()
    {
        $this->_model->getWriter();
        $this->_model->setWriter(new Mage_ImportExport_Model_Export_Adapter_Csv());
        $this->assertInstanceOf('Mage_ImportExport_Model_Export_Adapter_Csv', $this->_model->getWriter());
    }

    /**
     * Test for method filterAttributeCollection
     */
    public function testFilterAttributeCollection()
    {
        /** @var $model Stub_Mage_ImportExport_Model_Export_Entity_V2_Abstract */
        $model = $this->getMockForAbstractClass('Stub_Mage_ImportExport_Model_Export_Entity_V2_Abstract');
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $collection = $model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttrs = array();
        /** @var $attribute Mage_Customer_Model_Attribute */
        foreach ($collection as $attribute) {
            $existedAttrs[] = $attribute->getAttributeCode();
        }
        $disabledAttrs = $model->getDisabledAttributes();
        foreach ($disabledAttrs as $attributeCode) {
            $this->assertNotContains(
                $attributeCode,
                $existedAttrs,
                'Disabled attribute "' . $attributeCode . '" existed in collection'
            );
        }
    }
}

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
abstract class Stub_Mage_ImportExport_Model_Export_Entity_V2_Abstract
    extends Mage_ImportExport_Model_Export_Entity_V2_Abstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}