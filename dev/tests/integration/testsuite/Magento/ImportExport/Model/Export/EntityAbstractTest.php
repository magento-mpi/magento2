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
 * Test for abstract export model
 */
class Magento_ImportExport_Model_Export_EntityAbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Magento_ImportExport_Model_Export_EntityAbstract
     */
    protected $_model;

    protected function setUp()
    {
        parent::setUp();

        /** @var Magento_TestFramework_ObjectManager  $objectManager */
        $objectManager = Magento_TestFramework_Helper_Bootstrap::getObjectManager();

        $storeConfig = $objectManager->get('Magento_Core_Model_Store_Config');
        $this->_model = $this->getMockForAbstractClass(
            'Magento_ImportExport_Model_Export_EntityAbstract', array($storeConfig)
        );
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
        $this->_model->setWriter(Mage::getModel('Magento_ImportExport_Model_Export_Adapter_Csv'));
        $this->assertInstanceOf('Magento_ImportExport_Model_Export_Adapter_Csv', $this->_model->getWriter());
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
        /** @var $model Stub_Magento_ImportExport_Model_Export_EntityAbstract */
        $model = $this->getMockForAbstractClass('Stub_Magento_ImportExport_Model_Export_EntityAbstract');
        $collection = Mage::getResourceModel('Magento_Customer_Model_Resource_Attribute_Collection');
        $collection = $model->filterAttributeCollection($collection);
        /**
         * Check that disabled attributes is not existed in attribute collection
         */
        $existedAttributes = array();
        /** @var $attribute Magento_Customer_Model_Attribute */
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
abstract class Stub_Magento_ImportExport_Model_Export_EntityAbstract
    extends Magento_ImportExport_Model_Export_EntityAbstract
{
    public function __construct(
        Magento_Core_Model_Store_Config $coreStoreConfig,
        Magento_Core_Model_App $app,
        Magento_ImportExport_Model_Export_Factory $collectionFactory,
        Magento_ImportExport_Model_Resource_CollectionByPagesIteratorFactory $resourceColFactory,
        array $data = array()
    ) {
        parent::__construct($coreStoreConfig, $app, $collectionFactory, $resourceColFactory, $data);
        $this->_disabledAttrs = array('default_billing', 'default_shipping');
    }
}
