<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @subpackage  unit_tests
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Product\Attribute\Backend;

class StockTest extends \PHPUnit_Framework_TestCase
{
    const ATTRIBUTE_NAME = 'quantity_and_stock_status';

    /**
     * @var \Magento\Catalog\Model\Product\Attribute\Backend\Stock
     */
    protected $_model;

    /**
     * @var \Magento\CatalogInventory\Model\Stock\Item
     */
    protected $_inventory;

    protected function setUp()
    {
        $this->_inventory = $this->getMock('Magento\CatalogInventory\Model\Stock\Item',
            array('getIsInStock', 'getQty', 'loadByProduct'), array(), '', false);

        $itemFactory = $this->getMock('Magento\CatalogInventory\Model\Stock\ItemFactory', array('create'),
            array(), '', false);
        $itemFactory->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->_inventory));

        $logger = $this->getMock('Magento\Core\Model\Logger', array(), array(), '', false);

        $this->_model = $this->getMock('Magento\Catalog\Model\Product\Attribute\Backend\Stock', array('getAttribute'),
            array($itemFactory, $logger));

        $attribute = $this->getMock('Magento\Object', array('getAttributeCode'));
        $attribute->expects($this->atLeastOnce())->method('getAttributeCode')
            ->will($this->returnValue(self::ATTRIBUTE_NAME));

        $this->_model->expects($this->atLeastOnce())->method('getAttribute')->will($this->returnValue($attribute));
    }

    public function testAfterLoad()
    {
        $this->_inventory->expects($this->once())->method('getIsInStock')->will($this->returnValue(1));
        $this->_inventory->expects($this->once())->method('getQty')->will($this->returnValue(5));
        $object = new \Magento\Object();
        $this->_model->afterLoad($object);
        $data = $object->getData();
        $this->assertEquals(1, $data[self::ATTRIBUTE_NAME]['is_in_stock']);
        $this->assertEquals(5, $data[self::ATTRIBUTE_NAME]['qty']);
    }

    public function testBeforeSave()
    {
        $object = new \Magento\Object(array(
            self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => 5),
            'stock_data'         => array('is_in_stock' => 2, 'qty' => 2)
        ));
        $stockData = $object->getStockData();
        $this->assertEquals(2, $stockData['is_in_stock']);
        $this->assertEquals(2, $stockData['qty']);
        $this->assertNotEmpty($object->getData(self::ATTRIBUTE_NAME));

        $this->_model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertEquals(1, $stockData['is_in_stock']);
        $this->assertEquals(5, $stockData['qty']);
        $this->assertNull($object->getData(self::ATTRIBUTE_NAME));
    }

    public function testBeforeSaveQtyIsEmpty()
    {
        $object = new \Magento\Object(array(
            self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => ''),
            'stock_data'         => array('is_in_stock' => 2, 'qty' => '')
        ));

        $this->_model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertNull($stockData['qty']);
    }

    public function testBeforeSaveQtyIsZero()
    {
        $object = new \Magento\Object(array(
            self::ATTRIBUTE_NAME => array('is_in_stock' => 1, 'qty' => 0),
            'stock_data'         => array('is_in_stock' => 2, 'qty' => 0)
        ));

        $this->_model->beforeSave($object);

        $stockData = $object->getStockData();
        $this->assertEquals(0, $stockData['qty']);
    }
}
