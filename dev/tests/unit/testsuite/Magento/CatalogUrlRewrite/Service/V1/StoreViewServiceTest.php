<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogUrlRewrite\Service\V1;

use Magento\TestFramework\Helper\ObjectManager;
use \Magento\Catalog\Model\Category;
use \Magento\Catalog\Model\Product;

class StoreViewServiceTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService */
    protected $storeViewService;

    /** @var \Magento\Eav\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var \Magento\Eav\Model\Entity\Attribute\AbstractAttribute|\PHPUnit_Framework_MockObject_MockObject */
    protected $attribute;

    /** @var \Magento\Framework\App\Resource|\PHPUnit_Framework_MockObject_MockObject */
    protected $resource;

    /** @var \Magento\Framework\DB\Adapter\AdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $connection;

    /** @var  \Magento\Framework\Db\Select|\PHPUnit_Framework_MockObject_MockObject */
    protected $select;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $this->config = $this->getMock('Magento\Eav\Model\Config', [], [], '', false);
        $this->attribute = $this->getMockBuilder('Magento\Eav\Model\Entity\Attribute\AbstractAttribute')
            ->disableOriginalConstructor()
            ->setMethods(['__wakeup', 'getBackendTable', 'getId',])
            ->getMockForAbstractClass();
        $this->select = $this->getMock('Magento\Framework\Db\Select', [], [], '', false);
        $this->connection = $this->getMock('Magento\Framework\DB\Adapter\AdapterInterface', [], [], '', false);
        $this->resource = $this->getMock('Magento\Framework\App\Resource', [], [], '', false);
        $this->resource->expects($this->any())->method('getConnection')->will($this->returnValue($this->connection));
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');

        $this->storeViewService = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Service\V1\StoreViewService',
            [
                'eavConfig' => $this->config,
                'resource' => $this->resource,
                'storeManager' => $this->storeManager
            ]
        );
    }

    /**
     * @return array
     */
    public function isRootCategoryForStoreDataProvider()
    {
        return [
            [1, 1, 1, true],
            [1, 2, 1, false],
            [2, 0, 1, false],
        ];
    }

    /**
     * @dataProvider isRootCategoryForStoreDataProvider
     */
    public function testIsRootCategoryForStore($categoryId, $rootCategoryId, $storeId, $result)
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $store->expects($this->once())->method('getRootCategoryId')->will($this->returnValue($rootCategoryId));
        $this->storeManager->expects($this->once())->method('getStore')->with($storeId)
            ->will($this->returnValue($store));

        $this->assertEquals($result, $this->storeViewService->isRootCategoryForStore($categoryId, $storeId));
    }

    /**
     * @return array
     */
    public function overriddenUrlKeyForStoreDataProvider()
    {
        return [
            [1, [1, 2], true],
            [1, [2, 3], false],
            [1, [], false],
        ];
    }

    /**
     * @dataProvider overriddenUrlKeyForStoreDataProvider
     */
    public function testDoesProductHaveOverriddenUrlKeyForStore($storeId, $fetchedStoreIds, $result)
    {
        $productId = 'product_id';
        $this->config->expects($this->once())->method('getAttribute')->with(Product::ENTITY, 'url_key')
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->once())->method('getBackendTable')->will($this->returnValue('backend_table'));
        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue('attribute-id'));
        $this->select->expects($this->once())->method('from')->with('backend_table', 'store_id')
            ->will($this->returnSelf());
        $this->select->expects($this->exactly(2))->method('where')->will($this->returnValueMap([
            ['attribute_id = ?', 'attribute-id', null, $this->select],
            ['entity_id = ?', $productId, null, $this->select],
        ]));
        $this->connection->expects($this->once())->method('select')->will($this->returnValue($this->select));
        $this->connection->expects($this->once())->method('fetchCol')->with($this->select)
            ->will($this->returnValue($fetchedStoreIds));

        $this->assertEquals(
            $result,
            $this->storeViewService->doesProductHaveOverriddenUrlKeyForStore($storeId, $productId)
        );
    }

    /**
     * @dataProvider overriddenUrlKeyForStoreDataProvider
     */
    public function testDoesCategoryHaveOverriddenUrlKeyForStore($storeId, $fetchedStoreIds, $result)
    {
        $categoryId = 'category_id';
        $this->config->expects($this->once())->method('getAttribute')->with(Category::ENTITY, 'url_key')
            ->will($this->returnValue($this->attribute));
        $this->attribute->expects($this->once())->method('getBackendTable')->will($this->returnValue('backend_table'));
        $this->attribute->expects($this->once())->method('getId')->will($this->returnValue('attribute-id'));
        $this->select->expects($this->once())->method('from')->with('backend_table', 'store_id')
            ->will($this->returnSelf());
        $this->select->expects($this->exactly(2))->method('where')->will($this->returnValueMap([
            ['attribute_id = ?', 'attribute-id', null, $this->select],
            ['entity_id = ?', $categoryId, null, $this->select],
        ]));
        $this->connection->expects($this->once())->method('select')->will($this->returnValue($this->select));
        $this->connection->expects($this->once())->method('fetchCol')->with($this->select)
            ->will($this->returnValue($fetchedStoreIds));

        $this->assertEquals(
            $result,
            $this->storeViewService->doesCategoryHaveOverriddenUrlKeyForStore($storeId, $categoryId)
        );
    }
}
