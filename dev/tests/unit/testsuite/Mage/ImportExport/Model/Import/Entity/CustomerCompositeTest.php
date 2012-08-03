<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_ImportExport_Model_Import_Entity_CustomerCompositeTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var Mage_ImportExport_Model_Import_Entity_CustomerComposite
     */
    protected $_model;

    /**
     * @var array
     */
    protected $_customerAttributes = array('firstname', 'lastname', 'dob');

    /**
     * @var array
     */
    protected $_addressAttributes = array('city', 'country', 'street');

    /**
     * List of mocked methods for customer and address entity adapters
     *
     * @var array
     */
    protected $_entityMockedMethods = array(
        'validateRow',
        'getErrorMessages',
        'getErrorsCount',
        'getErrorsLimit',
        'getInvalidRowsCount',
        'getNotices',
        'getProcessedEntitiesCount',
        'setParameters',
        'setSource',
        'importData',
    );

    /**
     * Expected prepared data after method Mage_ImportExport_Model_Import_Entity_CustomerComposite::_prepareRowForDb
     *
     * @var array
     */
    protected $_preparedData = array(
        '_scope' => Mage_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT,
        Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE    => 'admin',
        Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL      => 'test@qwewqeq.com',
        Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => null,
    );

    /**
     * @return Mage_ImportExport_Model_Import_Entity_CustomerComposite
     */
    protected function _getModelMock()
    {
        $data = $this->_getModelDependencies();
        $data['customer_entity'] = $this->_getCustomerEntityMock();
        $data['address_entity']  = $this->_getAddressEntityMock();
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        return $this->_model;
    }

    /**
     * Returns entity mock for method testPrepareRowForDb
     *
     * @return Mage_ImportExport_Model_Import_Entity_CustomerComposite
     */
    protected function _getModelMockForPrepareRowForDb()
    {
        $customerEntity = $this->_getCustomerEntityMock(array('validateRow'));
        $customerEntity->expects($this->any())
            ->method('validateRow')
            ->will($this->returnValue(true));

        $customerStorage = $this->getMock('stdClass', array('getCustomerId'));
        $customerStorage->expects($this->any())
            ->method('getCustomerId')
            ->will($this->returnValue(1));

        $addressEntity = $this->_getAddressEntityMock(array('validateRow', 'getCustomerStorage'));
        $addressEntity->expects($this->any())
            ->method('validateRow')
            ->will($this->returnValue(true));
        $addressEntity->expects($this->any())
            ->method('getCustomerStorage')
            ->will($this->returnValue($customerStorage));

        $dataSourceMock = $this->getMock('stdClass', array('cleanBunches', 'saveBunch'));
        $dataSourceMock->expects($this->once())
            ->method('saveBunch')
            ->will($this->returnCallback(array($this, 'verifyPrepareRowForDbData')));

        $jsonHelper = $this->getMock('stdClass', array('jsonEncode'));

        $data = $this->_getModelDependencies();
        $data['customer_entity']   = $customerEntity;
        $data['address_entity']    = $addressEntity;
        $data['data_source_model'] = $dataSourceMock;
        $data['json_helper']       = $jsonHelper;
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        return $this->_model;
    }

    /**
     * Returns entity mock for method testImportData
     *
     * @param bool $isDeleteBehavior
     * @return Mage_ImportExport_Model_Import_Entity_CustomerComposite
     */
    protected function _getModelMockForImportData($isDeleteBehavior = false)
    {
        $customerEntity = $this->_getCustomerEntityMock();
        $customerEntity->expects($this->once())
            ->method('importData');

        $addressEntity = $this->_getAddressEntityMock();
        if ($isDeleteBehavior) {
            $addressEntity->expects($this->never())
                ->method('importData');
        } else {
            $addressEntity->expects($this->once())
                ->method('importData');
        }

        $data = $this->_getModelDependencies();
        $data['customer_entity'] = $customerEntity;
        $data['address_entity']  = $addressEntity;
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        return $this->_model;
    }

    protected function tearDown()
    {
        unset($this->_model);
    }

    /**
     * @param array $mockedMethods
     * @return Mage_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getCustomerEntityMock(array $mockedMethods = null)
    {
        if (is_null($mockedMethods)) {
            $mockedMethods = $this->_entityMockedMethods;
        }
        $mockedMethods[] = 'getAttributeCollection';
        $mockedMethods[] = 'getWebsiteId';

        /** @var $customerEntity Mage_ImportExport_Model_Import_Entity_Eav_Customer */
        $customerEntity = $this->getMock('Mage_ImportExport_Model_Import_Entity_Eav_Customer', $mockedMethods, array(),
            '', false
        );

        $attributeList = array();
        foreach ($this->_customerAttributes as $code) {
            $attribute = new Varien_Object(array(
                'attribute_code' => $code
            ));
            $attributeList[] = $attribute;
        }
        $customerEntity->expects($this->once())
            ->method('getAttributeCollection')
            ->will($this->returnValue($attributeList));

        return $customerEntity;
    }

    /**
     * @param array $mockedMethods
     * @return Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getAddressEntityMock(array $mockedMethods = null)
    {
        if (is_null($mockedMethods)) {
            $mockedMethods = $this->_entityMockedMethods;
        }
        $mockedMethods[] = 'getAttributeCollection';

        /** @var $addressEntity Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address */
        $addressEntity = $this->getMock('Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address', $mockedMethods,
            array(), '', false
        );

        $attributeList = array();
        foreach ($this->_addressAttributes as $code) {
            $attribute = new Varien_Object(array(
                'attribute_code' => $code
            ));
            $attributeList[] = $attribute;
        }
        $addressEntity->expects($this->once())
            ->method('getAttributeCollection')
            ->will($this->returnValue($attributeList));

        return $addressEntity;
    }

    /**
     * Retrieve all necessary objects mocks which used inside customer storage
     *
     * @return array
     */
    protected function _getModelDependencies()
    {
        $mageHelper = $this->getMock('Mage_ImportExport_Helper_Data', array('__'));
        $mageHelper->expects($this->any())
            ->method('__')
            ->will($this->returnArgument(0));

        $data = array(
            'data_source_model'            => 'not_used',
            'customer_data_source_model'   => 'not_used',
            'address_data_source_model'    => 'not_used',
            'connection'                   => 'not_used',
            'helpers'                      => array('Mage_ImportExport_Helper_Data' => $mageHelper),
            'json_helper'                  => 'not_used',
            'string_helper'                => new Mage_Core_Helper_String(),
            'page_size'                    => 1,
            'max_data_size'                => 1,
            'bunch_size'                   => 1,
            'collection_by_pages_iterator' => 'not_used',
            'next_customer_id'             => 1
        );

        return $data;
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::isAttributeParticular
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_initAddressAttributes
     */
    public function testIsAttributeParticular()
    {
        $this->_getModelMock();
        foreach ($this->_addressAttributes as $code) {
            $this->assertTrue(
                $this->_model->isAttributeParticular(
                    Mage_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . $code
                ),
                'Attribute must be particular'
            );
        }
        $this->assertFalse($this->_model->isAttributeParticular('test'), 'Attribute must not be particular');
    }

    /**
     * @return array
     */
    public function getRowDataProvider()
    {
        return array(
            'customer row' => array(
                '$rowData' => array(
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL   => 'test@test.com',
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => 'admin',
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => null
                ),
                '$scope'   => Mage_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT
            ),
            'address row'  => array(
                '$rowData' => array(
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL   => '',
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => '',
                    Mage_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => 1
                ),
                '$scope'   => Mage_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_ADDRESS
            )
        );
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::setParameters
     */
    public function testSetParameters()
    {
        $customerEntity = $this->_getCustomerEntityMock();
        $addressEntity  = $this->_getAddressEntityMock();

        $customerEntity->expects($this->once())
            ->method('setParameters')
            ->will($this->returnCallback(array($this, 'callbackCheckParameters')));
        $addressEntity->expects($this->once())
            ->method('setParameters')
            ->will($this->returnCallback(array($this, 'callbackCheckParameters')));
        $data = $this->_getModelDependencies();
        $data['customer_entity'] = $customerEntity;
        $data['address_entity']  = $addressEntity;
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        $params = array(
            'behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_APPEND
        );
        $this->_model->setParameters($params);
    }

    /**
     * @param array $params
     */
    public function callbackCheckParameters(array $params)
    {
        $this->assertArrayHasKey('behavior', $params);
        $this->assertEquals(Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE, $params['behavior']);
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::setSource
     */
    public function testSetSource()
    {
        $customerEntity = $this->_getCustomerEntityMock();
        $addressEntity  = $this->_getAddressEntityMock();

        $customerEntity->expects($this->once())
            ->method('setSource');
        $addressEntity->expects($this->once())
            ->method('setSource');
        $data = $this->_getModelDependencies();
        $data['customer_entity'] = $customerEntity;
        $data['address_entity']  = $addressEntity;
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        $source = $this->getMockForAbstractClass('Mage_ImportExport_Model_Import_Adapter_Abstract', array(), '', false);
        $this->_model->setSource($source);
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::setErrorMessages
     */
    public function testGetErrorMessages()
    {
        $errorMessages = array(
            'Required field' => array(1,2,3),
            'Bad password'   => array(1),
            'Wrong website'  => array(1,2)
        );
        $customerEntity = $this->_getCustomerEntityMock();
        $customerEntity->expects($this->once())
            ->method('getErrorMessages')
            ->will($this->returnValue($errorMessages));

        $errorMessages = array(
            'Required field'   => array(2,3,4,5),
            'Wrong address'  => array(1,2)
        );
        $addressEntity = $this->_getAddressEntityMock();
        $addressEntity->expects($this->once())
            ->method('getErrorMessages')
            ->will($this->returnValue($errorMessages));

        $data = $this->_getModelDependencies();
        $data['customer_entity'] = $customerEntity;
        $data['address_entity']  = $addressEntity;
        $this->_model = new Mage_ImportExport_Model_Import_Entity_CustomerComposite($data);

        $this->_model->addRowError('Bad password', 1);

        $expectedErrors = array(
            'Required field' => array(1,2,3,4,5),
            'Bad password'   => array(2),
            'Wrong website'  => array(1,2),
            'Wrong address'  => array(1,2)
        );

        $actualErrors = $this->_model->getErrorMessages();
        foreach ($expectedErrors as $error => $rows) {
            $this->assertArrayHasKey($error, $actualErrors);
            $this->assertSame($rows, array_values($actualErrors[$error]));
        }
    }

    /**
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_prepareRowForDb
     */
    public function testPrepareRowForDb()
    {
        $this->_getModelMockForPrepareRowForDb();
        $pathToCsvFile = __DIR__ . '/_files/customer_composite_prepare_row_for_db.csv';
        $source = new Mage_ImportExport_Model_Import_Adapter_Csv($pathToCsvFile);
        $this->_model->setSource($source);
        $this->_model->validateData();  // assertions processed in self::verifyPrepareRowForDbData
    }

    /**
     * Callback for Mage_ImportExport_Model_Resource_Import_Data::saveBunch to verify correctness of data
     * for method Mage_ImportExport_Model_Import_Entity_CustomerComposite::_prepareRowForDb
     *
     * @param string $entityType
     * @param string $behavior
     * @param array $bunchRows
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function verifyPrepareRowForDbData($entityType, $behavior, $bunchRows)
    {
        // source data contains only one record
        $this->assertCount(1, $bunchRows);

        // array must has all expected data
        $customerData = $bunchRows[0];
        foreach ($this->_preparedData as $expectedKey => $expectedValue) {
            $this->assertArrayHasKey($expectedKey, $customerData);
            $this->assertEquals($expectedValue, $customerData[$expectedKey]);
        }
    }

    /**
     * Data provider for method testImportData
     *
     * @return array
     */
    public function dataProviderTestImportData()
    {
        return array(
            'not_delete_behavior' => array(
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE
            ),
            'delete_behavior' => array(
                '$behavior' => Mage_ImportExport_Model_Import::BEHAVIOR_DELETE
            ),
        );
    }

    /**
     * @dataProvider dataProviderTestImportData
     * @covers Mage_ImportExport_Model_Import_Entity_CustomerComposite::_importData
     */
    public function testImportData($behavior)
    {
        $isDeleteBehavior = $behavior == Mage_ImportExport_Model_Import::BEHAVIOR_DELETE;
        $entityMock = $this->_getModelMockForImportData($isDeleteBehavior);
        $entityMock->setParameters(array('behavior' => $behavior));
        $entityMock->importData();
    }
}
