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
 * Test for abstract export model
 */
class Mage_ImportExport_Model_Export_EntityAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Export_EntityAbstract
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();
        $this->_model = $this->getMockForAbstractClass('Mage_ImportExport_Model_Export_EntityAbstract');
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
     */
    public function testGetWriter()
    {
        $this->_model->setWriter(Mage::getModel('Mage_ImportExport_Model_Export_Adapter_Csv'));
        $this->assertInstanceOf('Mage_ImportExport_Model_Export_Adapter_Csv', $this->_model->getWriter());
    }

    /**
     * Check that method throw exception when writer was not defined
     *
     * @expectedException Magento_Core_Exception
     */
    public function testGetWriterThrowsException()
    {
        $this->_model->getWriter();
    }

    /**
     * Test for method filterAttributeCollection
     */
    public function testFilterAttributeCollection()
    {
        /** @var $model Stub_Mage_ImportExport_Model_Export_EntityAbstract */
        $model = $this->getMockForAbstractClass('Stub_Mage_ImportExport_Model_Export_EntityAbstract');
        $collection = Mage::getResourceModel('Mage_Customer_Model_Resource_Attribute_Collection');
        $collection = $model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttributes = array();
        /** @var $attribute Mage_Customer_Model_Attribute */
        foreach ($collection as $attribute) {
            $existedAttributes[] = $attribute->getAttributeCode();
        }
        $disabledAttributes = $model->getDisabledAttributes();
        foreach ($disabledAttributes as $attributeCode) {
            $this->assertNotContains(
                $attributeCode,
                $existedAttributes,
                'Disabled attribute "' . $attributeCode . '" existed in collection'
            );
        }
    }
}

/**
 * Stub abstract class which provide to change protected property "$_disabledAttrs" and test methods depended on it
 */
abstract class Stub_Mage_ImportExport_Model_Export_EntityAbstract
    extends Mage_ImportExport_Model_Export_EntityAbstract
{
    public function __construct()
    {
        parent::__construct();
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
