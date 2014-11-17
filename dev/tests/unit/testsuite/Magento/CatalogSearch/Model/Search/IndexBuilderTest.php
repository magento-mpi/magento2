<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Search;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

/**
 * Test for \Magento\CatalogSearch\Model\Search\IndexBuilder
 */
class IndexBuilderTest extends \PHPUnit_Framework_TestCase
{

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface|MockObject */
    private $adapter;

    /** @var \Magento\Framework\DB\Select|MockObject */
    private $select;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface|MockObject */
    private $config;

    /** @var \Magento\Framework\Search\RequestInterface|MockObject */
    private $request;

    /** @var \Magento\Framework\App\Resource|MockObject */
    private $resource;

    /**
     * @var \Magento\CatalogSearch\Model\Search\IndexBuilder
     */
    private $target;

    protected function setUp()
    {
        $this->select = $this->getMockBuilder('\Magento\Framework\DB\Select')
            ->disableOriginalConstructor()
            ->setMethods(['from', 'joinLeft', 'where'])
            ->getMock();

        $this->adapter = $this->getMockBuilder('\Magento\Framework\DB\Adapter\AdapterInterface')
            ->disableOriginalConstructor()
            ->setMethods(['select'])
            ->getMockForAbstractClass();
        $this->adapter->expects($this->once())
            ->method('select')
            ->will($this->returnValue($this->select));

        $this->resource = $this->getMockBuilder('\Magento\Framework\App\Resource')
            ->disableOriginalConstructor()
            ->setMethods(['getConnection', 'getTableName'])
            ->getMock();
        $this->resource->expects($this->once())
            ->method('getConnection')
            ->with(\Magento\Framework\App\Resource::DEFAULT_READ_RESOURCE)
            ->will($this->returnValue($this->adapter));

        $this->request = $this->getMockBuilder('\Magento\Framework\Search\RequestInterface')
            ->disableOriginalConstructor()
            ->setMethods(['getIndex'])
            ->getMockForAbstractClass();

        $this->config = $this->getMockBuilder('\Magento\Framework\App\Config\ScopeConfigInterface')
            ->disableOriginalConstructor()
            ->setMethods(['isSetFlag'])
            ->getMockForAbstractClass();

        $objectManagerHelper = new ObjectManagerHelper($this);
        $this->target = $objectManagerHelper->getObject(
            'Magento\CatalogSearch\Model\Search\IndexBuilder',
            [
                'resource' => $this->resource,
                'config' => $this->config,
            ]
        );
    }


    public function testBuildWithOutOfStock()
    {
        $index = 'test_name_of_index';
        $table = 'test_table_index_name';

        $this->request->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue($index));

        $this->resource->expects($this->once())
            ->method('getTableName')
            ->with($index)
            ->will($this->returnValue($table));

        $this->select->expects($this->once())
            ->method('from')
            ->with(
                ['search_index' => $table],
                ['entity_id' => 'search_index.product_id']
            )
            ->will($this->returnSelf());

        $this->select->expects($this->at(1))
            ->method('joinLeft')
            ->with(
                ['category_index' => 'catalog_category_product_index'],
                'search_index.product_id = category_index.product_id'
                . ' AND search_index.store_id = category_index.store_id',
                []
            )
            ->will($this->returnSelf());

        $this->config->expects($this->once())
            ->method('isSetFlag')
            ->with('cataloginventory/options/show_out_of_stock')
            ->will($this->returnValue(true));

        $result = $this->target->build($this->request);
        $this->assertSame($this->select, $result);
    }

    public function testBuildWithoutOutOfStock()
    {
        $index = 'test_index_name';
        $table = 'test_index_table_name';

        $this->request->expects($this->once())
            ->method('getIndex')
            ->will($this->returnValue($index));

        $this->resource->expects($this->once())
            ->method('getTableName')
            ->with($index)
            ->will($this->returnValue($table));

        $this->select->expects($this->once())
            ->method('from')
            ->with(
                ['search_index' => $table],
                ['entity_id' => 'search_index.product_id']
            )
            ->will($this->returnSelf());

        $this->select->expects($this->at(1))
            ->method('joinLeft')
            ->with(
                ['category_index' => 'catalog_category_product_index'],
                'search_index.product_id = category_index.product_id'
                . ' AND search_index.store_id = category_index.store_id',
                []
            )
            ->will($this->returnSelf());

        $this->config->expects($this->once())
            ->method('isSetFlag')
            ->with('cataloginventory/options/show_out_of_stock')
            ->will($this->returnValue(false));

        $this->select->expects($this->at(2))
            ->method('joinLeft')
            ->with(
                ['stock_index' => 'cataloginventory_stock_status'],
                'search_index.product_id = stock_index.product_id'
                . ' AND stock_index.website_id = 1 AND stock_index.stock_id = 1',
                []
            )
            ->will($this->returnSelf());
        $this->select->expects($this->once())
            ->method('where')
            ->with('stock_index.stock_status = ?', 1)
            ->will($this->returnSelf());

        $result = $this->target->build($this->request);
        $this->assertSame($this->select, $result);
    }
}
