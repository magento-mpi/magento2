<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Product\CopyConstructor;

class CatalogInventoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Product\CopyConstructor\CatalogInventory
     */
    protected $_model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_productMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_duplicateMock;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $_stockItemMock;

    protected function setUp()
    {
        $this->_model = new \Magento\CatalogInventory\Model\Product\CopyConstructor\CatalogInventory();

        $this->_productMock   = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('__wakeup', 'getStockItem'), array(), '', false
        );

        $this->_duplicateMock = $this->getMock(
            '\Magento\Catalog\Model\Product',
            array('setStockData', 'unsStockItem', '__wakeup'), array(), '', false
        );

        $this->_stockItemMock = $this->getMock(
            'Magento\CatalogInventory\Model\Stock\Item', array(), array(), '', false
        );
    }

    public function testBuildWithoutCurrentProductStockItem()
    {
        $expectedData = array(
            'use_config_min_qty'          => 1,
            'use_config_min_sale_qty'     => 1,
            'use_config_max_sale_qty'     => 1,
            'use_config_backorders'       => 1,
            'use_config_notify_stock_qty' => 1
        );
        $this->_duplicateMock->expects($this->once())->method('unsStockItem');
        $this->_productMock->expects($this->once())->method('getStockItem')->will($this->returnValue(null));

        $this->_duplicateMock->expects($this->once())->method('setStockData')->with($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }

    public function testBuildWithCurrentProductStockItem()
    {
        $expectedData = array(
            'use_config_min_qty'          => 1,
            'use_config_min_sale_qty'     => 1,
            'use_config_max_sale_qty'     => 1,
            'use_config_backorders'       => 1,
            'use_config_notify_stock_qty' => 1,
            'use_config_enable_qty_inc'   => 'use_config_enable_qty_inc',
            'enable_qty_increments'       => 'enable_qty_increments',
            'use_config_qty_increments'   => 'use_config_qty_increments',
            'qty_increments'              => 'qty_increments',
        );
        $this->_duplicateMock->expects($this->once())->method('unsStockItem');
        $this->_productMock->expects($this->once())
            ->method('getStockItem')->will($this->returnValue($this->_stockItemMock));

        $this->_stockItemMock->expects($this->any())->method('getData')->will($this->returnArgument(0));

        $this->_duplicateMock->expects($this->once())->method('setStockData')->with($expectedData);

        $this->_model->build($this->_productMock, $this->_duplicateMock);
    }
}
