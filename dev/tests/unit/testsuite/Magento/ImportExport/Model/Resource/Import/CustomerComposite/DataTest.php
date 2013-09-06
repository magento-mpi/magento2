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
 * Test class for Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data
 */
class Magento_ImportExport_Model_Resource_Import_CustomerComposite_DataTest extends PHPUnit_Framework_TestCase
{
    /**
     * Array of customer attributes
     *
     * @var array
     */
    protected $_customerAttributes = array('customer_attribute1', 'customer_attribute2');

    /**
     * Generate dependencies for model
     *
     * @param string $entityType
     * @param array $bunchData
     * @return array
     */
    protected function _getDependencies($entityType, $bunchData)
    {
        /** @var $statementMock Magento_DB_Statement_Pdo_Mysql */
        $statementMock = $this->getMock('Magento_DB_Statement_Pdo_Mysql', array('setFetchMode', 'getIterator'), array(),
            '', false
        );
        $statementMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new ArrayIterator($bunchData)));

        /** @var $selectMock Magento_DB_Select */
        $selectMock = $this->getMock('Magento_DB_Select', array('from', 'order'),
            array(), '', false
        );
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('order')
            ->will($this->returnSelf());

        /** @var $adapterMock Magento_DB_Adapter_Pdo_Mysql */
        $adapterMock = $this->getMock('Magento_DB_Adapter_Pdo_Mysql', array('select', 'from', 'order', 'query'),
            array(), '', false
        );
        $adapterMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statementMock));

        /** @var $resourceModelMock Magento_Core_Model_Resource */
        $resourceModelMock = $this->getMock('Magento_Core_Model_Resource', array('_newConnection', 'getTableName'),
            array(), '', false
        );
        $resourceModelMock->expects($this->any())
            ->method('_newConnection')
            ->will($this->returnValue($adapterMock));
        $resourceModelMock->createConnection('core_write', '', array());

        $data = array(
            'json_helper' => new Magento_Core_Helper_Data(
                $this->getMock('Magento_Core_Helper_Context', array(), array(), '', false, false),
                $this->getMock('Magento_Core_Model_Config_Modules', array(), array(), '', false, false),
                $this->getMock('Magento_Core_Model_Store_Config', array(), array(), '', false),
                $this->getMock('Magento_Core_Model_Config', array(), array(), '', false)
            ),
            'resource'    => $resourceModelMock,
            'entity_type' => $entityType
        );

        if ($entityType == Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_ADDRESS) {
            $data['customer_attributes'] = $this->_customerAttributes;
        }

        return $data;
    }

    /**
     * @covers Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data::getNextBunch
     * @covers Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data::_prepareRow
     * @covers Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data::_prepareAddressRowData
     *
     * @dataProvider getNextBunchDataProvider
     * @param string $entityType
     * @param array $bunchData
     * @param array $expectedData
     */
    public function testGetNextBunch($entityType, $bunchData, $expectedData)
    {
        $dependencies = $this->_getDependencies($entityType, $bunchData);

        $resource = $dependencies['resource'];
        $coreHelper = $dependencies['json_helper'];
        unset($dependencies['resource'], $dependencies['json_helper']);

        $object = new Magento_ImportExport_Model_Resource_Import_CustomerComposite_Data($resource, $coreHelper,
            $dependencies
        );
        $this->assertEquals($expectedData, $object->getNextBunch());
    }

    /**
     * Data provider of row data and expected result of getNextBunch() method
     *
     * @return array
     */
    public function getNextBunchDataProvider()
    {
        return array(
            'address entity' => array(
                '$entityType' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_ADDRESS,
                '$bunchData'    => array(array(Zend_Json::encode(array(
                    array(
                        '_scope' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT,
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE => 'website1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL => 'email1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => null,
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute1'
                            => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(
                    0 => array(
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE => 'website1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL     => 'email1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => NULL,
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'attribute1' => 'value',
                        'attribute2' => 'value'
                    ),
                ),
            ),
            'customer entity default scope' => array(
                '$entityType' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER,
                '$bunchData'    => array(array(Zend_Json::encode(array(
                    array(
                        '_scope' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_DEFAULT,
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE => 'website1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL => 'email1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => null,
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute1'
                            => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(
                    0 => array(
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE => 'website1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL     => 'email1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => NULL,
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute1'
                            => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute2'
                            => 'value'
                    ),
                ),
            ),
            'customer entity address scope' => array(
                '$entityType' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::COMPONENT_ENTITY_CUSTOMER,
                '$bunchData'    => array(array(Zend_Json::encode(array(
                    array(
                        '_scope' => Magento_ImportExport_Model_Import_Entity_CustomerComposite::SCOPE_ADDRESS,
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_WEBSITE => 'website1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_EMAIL => 'email1',
                        Magento_ImportExport_Model_Import_Entity_Eav_Customer_Address::COLUMN_ADDRESS_ID => null,
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute1'
                            => 'value',
                        Magento_ImportExport_Model_Import_Entity_CustomerComposite::COLUMN_ADDRESS_PREFIX . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(),
            ),
        );
    }
}
