<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Mage_Eav
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Mage_Eav_Model_Entity_AbstractTest extends PHPUnit_Framework_TestCase
{
    /**
     * Entity model to be tested
     * @var Mage_Eav_Model_Entity_Abstract|PHPUnit_Framework_MockObject_MockObject
     */
    protected $_model;

    protected function setUp()
    {
        $this->_model = $this->getMockForAbstractClass('Mage_Eav_Model_Entity_Abstract');
    }

    protected function tearDown()
    {
        $this->_model = null;
    }

    /**
     * @param array $attribute1Sort
     * @param array $attribute2Sort
     * @param float $expected
     *
     * @dataProvider compareAttributesDataProvider
     */
    public function testCompareAttributes($attribute1Sort, $attribute2Sort, $expected)
    {
        $attribute1 = $this->getMock('Mage_Eav_Model_Entity_Attribute', null, array(), '', false);
        $attribute1->setAttributeSetInfo(array(0 => $attribute1Sort));
        $attribute2 = $this->getMock('Mage_Eav_Model_Entity_Attribute', null, array(), '', false);
        $attribute2->setAttributeSetInfo(array(0 => $attribute2Sort));
        $this->assertEquals($expected, $this->_model->attributesCompare($attribute1, $attribute2));
    }

    public static function compareAttributesDataProvider()
    {
        return array(
            'attribute1 bigger than attribute2' => array(
                'attribute1Sort' => array(
                    'group_sort' => 7,
                    'sort' => 5
                ),
                'attribute2Sort' => array(
                    'group_sort' => 5,
                    'sort' => 10
                ),
                'expected' => 1
            ),
            'attribute1 smaller than attribute2' => array(
                'attribute1Sort' => array(
                    'group_sort' => 7,
                    'sort' => 5
                ),
                'attribute2Sort' => array(
                    'group_sort' => 7,
                    'sort' => 10
                ),
                'expected' => -1
            ),
            'attribute1 equals to attribute2' => array(
                'attribute1Sort' => array(
                    'group_sort' => 7,
                    'sort' => 5
                ),
                'attribute2Sort' => array(
                    'group_sort' => 7,
                    'sort' => 5
                ),
                'expected' => 0
            ),
        );
    }

    /**
     * Get attribute list
     *
     * @return array
     */
    protected function _getAttributes()
    {
        $attributes = array();
        $codes = array('entity_type_id', 'attribute_set_id', 'created_at', 'updated_at', 'parent_id', 'increment_id');
        foreach ($codes as $code) {
            $mock = $this->getMock(
                'Mage_Eav_Model_Entity_Attribute_Abstract',
                array('getBackend', 'getBackendTable'),
                array(),
                '',
                false
            );
            $mock->setAttributeId($code);

            /** @var $backendModel Mage_Eav_Model_Entity_Attribute_Backend_Abstract */
            $backendModel = $this->getMock(
                'Mage_Eav_Model_Entity_Attribute_Backend_Abstract',
                array('getBackend', 'getBackendTable')
            );

            $backendModel->setAttribute($mock);

            $mock->expects($this->any())
                ->method('getBackend')
                ->will($this->returnValue($backendModel));

            $mock->expects($this->any())
                ->method('getBackendTable')
                ->will($this->returnValue($code.'_table'));

            $attributes[$code] = $mock;
        }
        return $attributes;
    }

    /**
     * Get adapter mock
     *
     * @return PHPUnit_Framework_MockObject_MockObject|Varien_Db_Adapter_Pdo_Mysql
     */
    private function _getAdapterMock()
    {
        $adapter = $this->getMock(
            'Varien_Db_Adapter_Pdo_Mysql',
            array(
                'describeTable', 'lastInsertId', 'insert', 'prepareColumnValue', 'query', 'delete'
            ),
            array(),
            '',
            false
        );
        $statement = $this->getMock(
            'Zend_Db_Statement',
            array('closeCursor', 'columnCount', 'errorCode', 'errorInfo', 'fetch', 'nextRowset', 'rowCount'),
            array(),
            '',
            false
        );

        $adapter->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statement));

        $adapter->expects($this->any())
            ->method('describeTable')
            ->will($this->returnValue(array('value' => array('test'))));

        $adapter->expects($this->any())
            ->method('prepareColumnValue')
            ->will($this->returnArgument(2));

        $adapter->expects($this->once())
            ->method('delete')
            ->with($this->equalTo('test_table'))
            ->will($this->returnValue(true));

        return $adapter;
    }

    public function testSave()
    {
        $code = 'test_attr';
        $set = 10;

        $object = $this->getMock('Mage_Catalog_Model_Product', null, array(), '', false);
        $object->setEntityTypeId(1);
        $object->setData(array(
            'test_attr' => 'test_attr',
            'attribute_set_id' => $set,
        ));

        $entityType = new Varien_Object();
        $entityType->setEntityTypeCode('test');
        $entityType->setEntityTypeId(0);
        $entityType->setEntityTable('table');

        $attributes = $this->_getAttributes();

        $attribute = $this->getMock(
            'Mage_Eav_Model_Entity_Attribute_Abstract',
            array('getBackend', 'getBackendTable', 'isInSet', 'getApplyTo'),
            array(),
            '',
            false
        );
        $attribute->setAttributeId($code);

        /** @var $backendModel Mage_Eav_Model_Entity_Attribute_Backend_Abstract */
        $backendModel = $this->getMock(
            'Mage_Eav_Model_Entity_Attribute_Backend_Abstract',
            array('getBackend', 'getBackendTable', 'getAffectedFields')
        );
        $backendModel->expects($this->once())
            ->method('getAffectedFields')
            ->will($this->returnValue(array(
                'test_table' => array(
                    array(
                        'value_id' => 0,
                        'attribute_id' => $code,
                    )
                )
            )));

        $backendModel->setAttribute($attribute);
        $attribute->expects($this->any())
            ->method('getBackend')
            ->will($this->returnValue($backendModel));

        $attribute->expects($this->any())
            ->method('getBackendTable')
            ->will($this->returnValue($code . '_table'));

        $attribute->expects($this->any())
            ->method('isInSet')
            ->with($this->equalTo($set))
            ->will($this->returnValue(false));

        $attributes[$code] = $attribute;


        $data = array(
            'type' => $entityType,
            'entityTable' => 'entityTable',
            'attributesByCode' => $attributes,
        );
        /** @var $model PHPUnit_Framework_MockObject_MockObject */
        $model = $this->getMockForAbstractClass(
            'Mage_Eav_Model_Entity_Abstract',
            array($data),
            '',
            true,
            true,
            true,
            array('_getConfig')
        );

        $configMock = $this->getMock('Mage_Eav_Model_Config', array(), array(), '', false);
        $model->expects($this->any())->method('_getConfig')->will($this->returnValue($configMock));

        $model->setConnection($this->_getAdapterMock());
        $model->isPartialSave(true);

        $model->save($object);
    }
}
