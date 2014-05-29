<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Framework\ModelResource\Db;

class AbstractDblTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Framework\Model\Resource\Db\AbstractDb
     */
    protected $_model;

    /**
     * @var \Magento\Framework\App\Resource
     */
    protected $_resourcesMock;

    protected function setUp()
    {
        $this->_resourcesMock = $this->getMock(
            '\Magento\Framework\App\Resource',
            ['getConnection', 'getTableName', 'getTable'],
            [],
            '',
            false
        );

        $this->_model = $this->getMockForAbstractClass(
            'Magento\Framework\Model\Resource\Db\AbstractDb',
            array($this->_resourcesMock)
        );
    }

    /**
     * @param $fieldNameType
     * @param $expectedResult
     * @dataProvider addUniqueFieldDataProvider
     */
    public function testAddUniqueField($fieldNameType, $expectedResult)
    {
        $this->_model->addUniqueField($fieldNameType);
        $this->assertEquals($expectedResult, $this->_model->getUniqueFields());
    }

    /**
     * @return array
     */
    public function addUniqueFieldDataProvider()
    {
        return [
            [
                'fieldNameString',
                ['fieldNameString']
            ],
            [
                [
                    'fieldNameArray',
                    'FieldNameArraySecond'
                ],
                [
                    [
                        'fieldNameArray',
                        'FieldNameArraySecond'
                    ]
                ]
            ],
            [
                null,
                [null]
            ]
        ];
    }

    public function testAddUniqueFieldArray()
    {
        $this->assertInstanceOf(
            '\Magento\Framework\Model\Resource\Db\AbstractDb',
            $this->_model->addUniqueField(['someField'])
        );
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Empty identifier field name
     */
    public function testGetIdFieldNameException()
    {
        $this->_model->getIdFieldName();
    }

    public function testGetIdFieldname()
    {
        $data = 'MainTableName';
        $idFieldNameProperty = new \ReflectionProperty(
            'Magento\Framework\Model\Resource\Db\AbstractDb', '_idFieldName'
        );
        $idFieldNameProperty->setAccessible(true);
        $idFieldNameProperty->setValue($this->_model, $data);
        $this->assertEquals($data, $this->_model->getIdFieldName());
    }

    /**
     * @expectedException \Magento\Framework\Model\Exception
     * @expectedExceptionMessage Empty main table name
     */
    public function testGetMainTableException()
    {
        $this->_model->getMainTable();
    }

    /**
     * @dataProvider getTableDataProvider
     * @param $tableName
     * @param $expected_result
     */
    public function testGetMainTable($tableName, $expected_result)
    {
        $mainTableProperty = new \ReflectionProperty('Magento\Framework\Model\Resource\Db\AbstractDb', '_mainTable');
        $mainTableProperty->setAccessible(true);
        $mainTableProperty->setValue($this->_model, $tableName);
        $this->_resourcesMock->expects($this->once())
            ->method('getTableName')
            ->with($expected_result)
            ->will($this->returnValue($expected_result)
            );
        $this->assertEquals($expected_result, $this->_model->getMainTable());
    }

    public function getTableDataProvider()
    {
        return [
            [
                'tableName',
                'tableName'
            ],
            [
                [
                    'tableName',
                    'entity_suffix'
                ],
                'tableName_entity_suffix'
            ]
        ];
    }

    public function testGetTable()
    {
        $data = 'tableName';
        $this->_resourcesMock->expects($this->once())->method('getTableName')->with($data)->will(
            $this->returnValue('tableName')
        );
        $tablesProperty = new \ReflectionProperty('Magento\Framework\Model\Resource\Db\AbstractDb', '_tables');
        $tablesProperty->setAccessible(true);
        $tablesProperty->setValue($this->_model, [$data]);
        $this->assertEquals($data, $this->_model->getTable($data));
    }

    public function testGetChecksumNegative()
    {
        $this->assertEquals(false, $this->_model->getChecksum(null));
    }

    /**
     * @dataProvider getChecksumProvider
     * @param $checksum
     * @param $expected
     */
    public function testGetChecksum($checksum, $expected)
    {
        $adapterInterfaceMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $adapterInterfaceMock->expects($this->once())->method('getTablesChecksum')->with($checksum)->will(
            $this->returnValue([$checksum => 'checksum'])
        );
        $this->_resourcesMock->expects($this->any())->method('getConnection')->will(
            $this->returnValue($adapterInterfaceMock)
        );
        $this->assertEquals($expected, $this->_model->getChecksum($checksum));
    }

    public function getChecksumProvider()
    {
        return [
            [
                'checksum', 'checksum'
            ],
            [
                14,
                'checksum'
            ]
        ];
    }

    public function testResetUniqueField()
    {
        $uniqueFields = new \ReflectionProperty('Magento\Framework\Model\Resource\Db\AbstractDb', '_uniqueFields');
        $uniqueFields->setAccessible(true);
        $uniqueFields->setValue($this->_model, ['uniqueField1', 'uniqueField2']);
        $this->_model->resetUniqueField();
        $this->assertEquals([], $this->_model->getUniqueFields());
    }

    public function testGetUniqueFields()
    {
        $uniqueFieldsReflection = new \ReflectionProperty('Magento\Framework\Model\Resource\Db\AbstractDb', '_uniqueFields');
        $uniqueFieldsReflection->setAccessible(true);
        $uniqueFieldsReflection->setValue($this->_model, null);
        $this->assertEquals([], $this->_model->getUniqueFields());
    }

    public function testGetValidationRulesBeforeSave()
    {
        $this->assertNull($this->_model->getValidationRulesBeforeSave());
    }

    public function testGetReadConnection()
    {
        $adapterInterfaceMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->_resourcesMock->expects($this->any())->method('getConnection')->will(
            $this->returnValue($adapterInterfaceMock)
        );
        $this->assertInstanceOf('\Magento\Framework\DB\Adapter\AdapterInterface', $this->_model->getReadConnection());
    }

    public function testGetReadAdapter()
    {
        $adapterInterfaceMock = $this->getMock('\Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $adapterInterfaceMock->expects($this->once())->method('getTransactionLevel')->will($this->returnValue(1));
        $this->_resourcesMock->expects($this->any())->method('getConnection')->will(
            $this->returnValue($adapterInterfaceMock)
        );
        $this->assertInstanceOf('\Magento\Framework\DB\Adapter\AdapterInterface', $this->_model->getReadConnection());
    }

    public function testLoad()
    {
        $contextMock = $this->getMock('\Magento\Framework\Model\Context', [], [], '', false);
        $registryMock = $this->getMock('\Magento\Framework\Registry', [], [], '', false);
        $abstractModelMock = $this->getMockForAbstractClass(
            '\Magento\Framework\Model\AbstractModel',
            [$contextMock, $registryMock],
            '',
            false,
            true,
            true,
            ['__wakeup']
        );

        $value = 'some_value';
        $field = $idFieldName = new \ReflectionProperty('Magento\Framework\Model\Resource\Db\AbstractDb', '_idFieldName');
        $idFieldName->setAccessible(true);
        $idFieldName->setValue($this->_model, 'field_value');

        $this->assertInstanceOf(
            'Magento\Framework\Model\Resource\Db\AbstractDb',
            $this->_model->load($abstractModelMock, $value, $field)
        );
    }
}
