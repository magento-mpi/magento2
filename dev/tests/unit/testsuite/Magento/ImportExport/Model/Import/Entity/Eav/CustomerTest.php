<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_ImportExport
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Test class for Magento_ImportExport_Model_Import_Entity_Eav_Customer
 */
class Magento_ImportExport_Model_Import_Entity_Eav_CustomerTest extends PHPUnit_Framework_TestCase
{
    /**
     * Customer entity import model
     *
     * @var Magento_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    /**
     * Available behaviours
     *
     * @var array
     */
    protected $_availableBehaviors = array(
        Magento_ImportExport_Model_Import::BEHAVIOR_ADD_UPDATE,
        Magento_ImportExport_Model_Import::BEHAVIOR_DELETE,
        Magento_ImportExport_Model_Import::BEHAVIOR_CUSTOM,
    );

    /**
     * Custom behavior input rows
     *
     * @var array
     */
    protected $_inputRows = array(
        'create' => array(
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_ACTION  => 'create',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL   => 'create@email.com',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => 'website1',
        ),
        'update' => array(
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_ACTION  => 'update',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL   => 'update@email.com',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => 'website1',
        ),
        'delete' => array(
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_ACTION
                => Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_ACTION_VALUE_DELETE,
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_EMAIL   => 'delete@email.com',
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_WEBSITE => 'website1',
        ),
    );

    /**
     * Customer ids for all custom behavior input rows
     *
     * @var array
     */
    protected $_customerIds = array(
        'create' => 1,
        'update' => 2,
        'delete' => 3,
    );

    /**
     * Unset entity adapter model
     */
    public function tearDown()
    {
        unset($this->_model);

        parent::tearDown();
    }

    /**
     * Create mock for import with custom behavior test
     *
     * @return Magento_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    protected function _getModelMockForTestImportDataWithCustomBehaviour()
    {
        // entity adapter mock
        $modelMock = $this->getMock(
            'Magento_ImportExport_Model_Import_Entity_Eav_Customer',
            array(
                'validateRow',
                '_getCustomerId',
                '_prepareDataForUpdate',
                '_saveCustomerEntities',
                '_saveCustomerAttributes',
                '_deleteCustomerEntities',
            ),
            array(),
            '',
            false,
            true,
            true
        );

        $availableBehaviors = new ReflectionProperty($modelMock, '_availableBehaviors');
        $availableBehaviors->setAccessible(true);
        $availableBehaviors->setValue($modelMock, $this->_availableBehaviors);

        // mock to imitate data source model
        $dataSourceModelMock = $this->getMock(
            'Magento_ImportExport_Model_Resource_Import_Data',
            array('getNextBunch'),
            array(),
            '',
            false
        );
        $dataSourceModelMock->expects($this->at(0))
            ->method('getNextBunch')
            ->will($this->returnValue($this->_inputRows));
        $dataSourceModelMock->expects($this->at(1))
            ->method('getNextBunch')
            ->will($this->returnValue(null));

        $property = new ReflectionProperty(
            'Magento_ImportExport_Model_Import_Entity_Eav_Customer',
            '_dataSourceModel'
        );
        $property->setAccessible(true);
        $property->setValue($modelMock, $dataSourceModelMock);

        $modelMock->expects($this->any())
            ->method('validateRow')
            ->will($this->returnValue(true));

        $modelMock->expects($this->any())
            ->method('_getCustomerId')
            ->will($this->returnValue($this->_customerIds['delete']));

        $modelMock->expects($this->any())
            ->method('_prepareDataForUpdate')
            ->will($this->returnCallback(array($this, 'prepareForUpdateMock')));

        $modelMock->expects($this->any())
            ->method('_saveCustomerEntities')
            ->will($this->returnCallback(array($this, 'validateSaveCustomerEntities')));

        $modelMock->expects($this->any())
            ->method('_saveCustomerAttributes')
            ->will($this->returnValue($modelMock));

        $modelMock->expects($this->any())
            ->method('_deleteCustomerEntities')
            ->will($this->returnCallback(array($this, 'validateDeleteCustomerEntities')));

        return $modelMock;
    }

    /**
     * Test whether correct methods are invoked in case of custom behaviour for each row in action column
     *
     * @covers Magento_ImportExport_Model_Import_Entity_Eav_Customer::_importData
     */
    public function testImportDataWithCustomBehaviour()
    {
        $this->_model = $this->_getModelMockForTestImportDataWithCustomBehaviour();
        $this->_model->setParameters(array('behavior' => Magento_ImportExport_Model_Import::BEHAVIOR_CUSTOM));

        // validation in validateSaveCustomerEntities and validateDeleteCustomerEntities
        $this->_model->importData();
    }

    /**
     * Emulate data preparing depending on value in row action column
     *
     * @param array $rowData
     * @return int
     */
    public function prepareForUpdateMock(array $rowData)
    {
        $preparedResult = array(
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::ENTITIES_TO_CREATE_KEY => array(),
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::ENTITIES_TO_UPDATE_KEY => array(),
            Magento_ImportExport_Model_Import_Entity_Eav_Customer::ATTRIBUTES_TO_SAVE_KEY => array('table' => array())
        );

        $actionColumnKey = Magento_ImportExport_Model_Import_Entity_Eav_Customer::COLUMN_ACTION;
        if ($rowData[$actionColumnKey] == 'create') {
            $preparedResult[Magento_ImportExport_Model_Import_Entity_Eav_Customer::ENTITIES_TO_CREATE_KEY] = array(
                array('entity_id' => $this->_customerIds['create'])
            );
        } elseif ($rowData[$actionColumnKey] == 'update') {
            $preparedResult[Magento_ImportExport_Model_Import_Entity_Eav_Customer::ENTITIES_TO_UPDATE_KEY] = array(
                array('entity_id' => $this->_customerIds['update'])
            );
        }

        return $preparedResult;
    }

    /**
     * Validation method for _saveCustomerEntities
     *
     * @param array $entitiesToCreate
     * @param array $entitiesToUpdate
     * @return Magento_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    public function validateSaveCustomerEntities(array $entitiesToCreate, array $entitiesToUpdate)
    {
        $this->assertCount(1, $entitiesToCreate);
        $this->assertEquals($this->_customerIds['create'], $entitiesToCreate[0]['entity_id']);
        $this->assertCount(1, $entitiesToUpdate);
        $this->assertEquals($this->_customerIds['update'], $entitiesToUpdate[0]['entity_id']);
        return $this->_model;
    }

    /**
     * Validation method for _deleteCustomerEntities
     *
     * @param array $customerIdsToDelete
     * @return Magento_ImportExport_Model_Import_Entity_Eav_Customer|PHPUnit_Framework_MockObject_MockObject
     */
    public function validateDeleteCustomerEntities(array $customerIdsToDelete)
    {
        $this->assertCount(1, $customerIdsToDelete);
        $this->assertEquals($this->_customerIds['delete'], $customerIdsToDelete[0]);

        return $this->_model;
    }
}
