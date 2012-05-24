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

class Enterprise_GiftRegistry_Model_Resource_Item_Option_CollectionTest
    extends Magento_Test_TestCase_ZendDbAdapterAbstract
{
    /**
     * @var Enterprise_GiftRegistry_Model_Resource_Item_Option_Collection
     */
    protected $_collection;

    public function setUp()
    {
        $this->_collection = $this->getMock('Enterprise_GiftRegistry_Model_Resource_Item_Option_Collection',
            array('getSelect'), array(), '', false);
    }

    /**
     * @param mixed $item
     * @param mixed $condition
     * @param mixed $return
     * @param string $expected
     * @dataProvider addProductFilterDataProvider
     */
    public function testAddProductFilter($item, $condition, $return, $expected)
    {
        $adapter = $this->_getAdapterMock(
            'Zend_Db_Adapter_Pdo_Mysql',
            array('fetchAll', 'prepareSqlCondition'),
            null
        );
        $adapter->expects($this->any())
            ->method('prepareSqlCondition')
            ->with(
                $this->stringContains('product_id'),
                $condition
            )
            ->will($this->returnValue($return));

        $this->_collection->setConnection($adapter);

        $select = $this->_collection->getSelectSql()->from('test');
        $this->_collection->expects($this->any())
            ->method('getSelect')
            ->will($this->returnValue($select));

        $this->_collection->addProductFilter($item);

        $this->assertContains($expected, $this->_collection->getSelect()->assemble());
    }

    public function addProductFilterDataProvider()
    {
        $product = $this->getMock('Mage_Catalog_Model_Product', array('getId'), array(), '', false);
        $product->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(93));
        return array(
            array(
                99, 99, 'product_id = 99', "SELECT `test`.* FROM `test` WHERE (product_id = 99)"
            ),
            array(
                array(95, 96), array('in'=>array(95, 96)), 'product_id IN(95, 96)',
                'SELECT `test`.* FROM `test` WHERE (product_id IN(95, 96))'
            ),
            array(
                null, 'null', '', 'SELECT `test`.* FROM `test`'
            ),
            array(
                $product, 93, 'product_id = 93', 'SELECT `test`.* FROM `test` WHERE (product_id = 93)'
            ),

        );
    }
}
