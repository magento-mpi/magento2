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
 * Test class for \Magento\ImportExport\Model\Resource\Import\CustomerComposite\Data
 */
namespace Magento\ImportExport\Model\Resource\Import\CustomerComposite;

class DataTest extends \PHPUnit_Framework_TestCase
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
        /** @var $statementMock \Magento\DB\Statement\Pdo\Mysql */
        $statementMock = $this->getMock('Magento\DB\Statement\Pdo\Mysql', array('setFetchMode', 'getIterator'), array(),
            '', false
        );
        $statementMock->expects($this->any())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator($bunchData)));

        /** @var $selectMock \Magento\DB\Select */
        $selectMock = $this->getMock('Magento\DB\Select', array('from', 'order'),
            array(), '', false
        );
        $selectMock->expects($this->any())
            ->method('from')
            ->will($this->returnSelf());
        $selectMock->expects($this->any())
            ->method('order')
            ->will($this->returnSelf());

        /** @var $adapterMock \Magento\DB\Adapter\Pdo\Mysql */
        $adapterMock = $this->getMock('Magento\DB\Adapter\Pdo\Mysql', array('select', 'from', 'order', 'query'),
            array(), '', false
        );
        $adapterMock->expects($this->any())
            ->method('select')
            ->will($this->returnValue($selectMock));
        $adapterMock->expects($this->any())
            ->method('query')
            ->will($this->returnValue($statementMock));

        /** @var $resourceModelMock \Magento\App\Resource */
        $resourceModelMock = $this->getMock('Magento\App\Resource',
            array('getConnection', '_newConnection', 'getTableName'),
            array(), '', false
        );
        $resourceModelMock->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($adapterMock));

        $data = array(
            'resource'    => $resourceModelMock,
            'entity_type' => $entityType
        );

        if ($entityType == \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COMPONENT_ENTITY_ADDRESS) {
            $data['customer_attributes'] = $this->_customerAttributes;
        }

        return $data;
    }

    /**
     * @covers \Magento\ImportExport\Model\Resource\Import\CustomerComposite\Data::getNextBunch
     * @covers \Magento\ImportExport\Model\Resource\Import\CustomerComposite\Data::_prepareRow
     * @covers \Magento\ImportExport\Model\Resource\Import\CustomerComposite\Data::_prepareAddressRowData
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
        $coreHelper = $this->getMock('Magento\Core\Helper\Data', array('__construct'), array(), '', false);
        unset($dependencies['resource'], $dependencies['json_helper']);

        $object = new \Magento\ImportExport\Model\Resource\Import\CustomerComposite\Data($resource, $coreHelper,
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
                '$entityType' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COMPONENT_ENTITY_ADDRESS,
                '$bunchData'    => array(array(\Zend_Json::encode(array(
                    array(
                        '_scope' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::SCOPE_DEFAULT,
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_WEBSITE => 'website1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_EMAIL => 'email1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_ADDRESS_ID => null,
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute1'
                            => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(
                    0 => array(
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_WEBSITE => 'website1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_EMAIL     => 'email1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_ADDRESS_ID => NULL,
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'attribute1' => 'value',
                        'attribute2' => 'value'
                    ),
                ),
            ),
            'customer entity default scope' => array(
                '$entityType' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COMPONENT_ENTITY_CUSTOMER,
                '$bunchData'    => array(array(\Zend_Json::encode(array(
                    array(
                        '_scope' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::SCOPE_DEFAULT,
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_WEBSITE => 'website1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_EMAIL => 'email1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_ADDRESS_ID => null,
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute1'
                            => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(
                    0 => array(
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_WEBSITE => 'website1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_EMAIL     => 'email1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_ADDRESS_ID => NULL,
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute1'
                            => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute2'
                            => 'value'
                    ),
                ),
            ),
            'customer entity address scope' => array(
                '$entityType' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COMPONENT_ENTITY_CUSTOMER,
                '$bunchData'    => array(array(\Zend_Json::encode(array(
                    array(
                        '_scope' => \Magento\ImportExport\Model\Import\Entity\CustomerComposite::SCOPE_ADDRESS,
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_WEBSITE => 'website1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_EMAIL => 'email1',
                        \Magento\ImportExport\Model\Import\Entity\Eav\Customer\Address::COLUMN_ADDRESS_ID => null,
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_BILLING => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_DEFAULT_SHIPPING => 'value',
                        'customer_attribute1' => 'value',
                        'customer_attribute2' => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute1'
                            => 'value',
                        \Magento\ImportExport\Model\Import\Entity\CustomerComposite::COLUMN_ADDRESS_PREFIX
                            . 'attribute2'
                            => 'value'
                    )
                )))),
                '$expectedData' => array(),
            ),
        );
    }
}
