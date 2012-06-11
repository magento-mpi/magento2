<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Enterprise_GiftRegistry
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

class Enterprise_GiftRegistry_Model_Resource_Item_CollectionTest extends Magento_Test_TestCase_ZendDbAdapterAbstract
{
    /**
     * @var Enterprise_GiftRegistry_Model_Resource_Item_Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = $this->getMock('Enterprise_GiftRegistry_Model_Resource_Item_Collection',
            array('getSelect'), array(), '', false);
    }

    public function testAddProductFilter()
    {
        $adapter =$this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );
        $adapter->expects($this->any())
            ->method('prepareSqlCondition')
            ->with(
                $this->stringContains('product_id'),
                99
            )
            ->will($this->returnValue('product_id = 99'));

        $this->_collection->setConnection($adapter);

        $select = $this->_collection->getSelectSql()->from('test');
        $this->_collection->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($select));

        $this->_collection->addProductFilter(99);

        $this->assertContains('product_id = 99', $this->_collection->getSelect()->assemble());
    }

    /**
     * @param int|array $item
     * @param string $expected
     * @dataProvider addItemFilterDataProvider
     */
    public function testAddItemFilter($item, $expected)
    {
        $adapter = $this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );

        if (is_array($item)) {
            $adapter->expects($this->any())
                ->method('prepareSqlCondition')
                ->with(
                    $this->stringContains('item_id'),
                    array('in' => $item)
                )
                ->will($this->returnValue("item_id IN(".implode(", ", $item).")"));

        } else {
            $adapter->expects($this->any())
                ->method('prepareSqlCondition')
                ->with(
                    $this->stringContains('item_id'),
                    $item
                )
                ->will($this->returnValue("item_id = $item"));
        }

        $this->_collection->setConnection($adapter);

        $select = $this->_collection->getSelectSql()->from('test');
        $this->_collection->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($select));

        $this->_collection->addItemFilter($item);

        $this->assertEquals($expected, $this->_collection->getSelect()->assemble());
    }

    public function addItemFilterDataProvider()
    {
        return array(
            array(99, "SELECT `test`.* FROM `test` WHERE (item_id = 99)"),
            array(array(95, 96), 'SELECT `test`.* FROM `test` WHERE (item_id IN(95, 96))'),
            array('null', 'SELECT `test`.* FROM `test`'),
        );
    }
}
