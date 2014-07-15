<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Product;

class ActionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\Catalog\Model\Product\Action
     */
    protected $model;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productWebsiteFactory;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $resource;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $productWebsite;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $indexIndexer;

    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $categoryIndexer;

    public function setUp()
    {
        $eventManagerMock = $this->getMock('Magento\Framework\Event\ManagerInterface');
        $this->productWebsiteFactory = $this->getMock(
            '\Magento\Catalog\Model\Product\WebsiteFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->resource = $this->getMock(
            '\Magento\Framework\Model\Resource\AbstractResource',
            ['updateAttributes', '_getWriteAdapter', '_getReadAdapter', '_construct', 'getIdFieldName'],
            [],
            '',
            false
        );
        $this->productWebsite = $this->getMock(
            '\Magento\Catalog\Model\Product\Website',
            ['addProducts', 'removeProducts', '__wakeup'],
            [],
            '',
            false
        );
        $this->productWebsiteFactory
            ->expects($this->any())
            ->method('create')
            ->will($this->returnValue($this->productWebsite));
        $this->indexIndexer = $this->getMock(
            '\Magento\Index\Model\Indexer',
            ['processEntityAction', '__wakeup'],
            [],
            '',
            false
        );
        $this->categoryIndexer = $this->getMock(
            '\Magento\Indexer\Model\Indexer',
            ['getId', 'load', 'isScheduled', 'reindexList'],
            [],
            '',
            false
        );
        $objectManager = new \Magento\TestFramework\Helper\ObjectManager($this);
        $this->model = $objectManager->getObject(
            '\Magento\Catalog\Model\Product\Action',
            [
                'eventDispatcher' => $eventManagerMock,
                'resource' => $this->resource,
                'productWebsiteFactory' => $this->productWebsiteFactory,
                'indexIndexer' => $this->indexIndexer,
                'categoryIndexer' => $this->categoryIndexer,
            ]
        );
    }

    public function testUpdateAttributes()
    {
        $productIds = [1];
        $attrData = [1];
        $storeId = 1;
        $this->resource
            ->expects($this->any())
            ->method('updateAttributes')
            ->with($productIds, $attrData, $storeId)
            ->will($this->returnSelf());
        $this->indexIndexer
            ->expects($this->any())
            ->method('processEntityAction')
            ->with($this->model, 'catalog_product', 'mass_action');
        $this->categoryIndexer
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(false));
        $this->categoryIndexer
            ->expects($this->any())
            ->method('load')
            ->with('catalog_product_category')
            ->will($this->returnSelf());
        $this->categoryIndexer
            ->expects($this->any())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->categoryIndexer
            ->expects($this->any())
            ->method('reindexList')
            ->will($this->returnValue($productIds));
        $this->assertEquals($this->model, $this->model->updateAttributes($productIds, $attrData, $storeId));
    }

    /**
     * @param $type
     * @param $methodName
     * @dataProvider updateWebsitesDataProvider
     */
    public function testUpdateWebsites($type, $methodName)
    {
        $productIds = [1];
        $websiteIds = [1];
        $this->productWebsite
            ->expects($this->any())
            ->method($methodName)
            ->with($websiteIds, $productIds)
            ->will($this->returnSelf());
        $this->indexIndexer
            ->expects($this->any())
            ->method('processEntityAction')
            ->with($this->model, 'catalog_product', 'mass_action');
        $this->categoryIndexer
            ->expects($this->any())
            ->method('getId')
            ->will($this->returnValue(false));
        $this->categoryIndexer
            ->expects($this->any())
            ->method('load')
            ->with('catalog_product_category')
            ->will($this->returnSelf());
        $this->categoryIndexer
            ->expects($this->any())
            ->method('isScheduled')
            ->will($this->returnValue(false));
        $this->categoryIndexer
            ->expects($this->any())
            ->method('reindexList')
            ->will($this->returnValue($productIds));
        $this->assertEquals(null, $this->model->updateWebsites($productIds, $websiteIds, $type));
    }

    public function updateWebsitesDataProvider()
    {
        return [
            ['$type' => 'add', '$methodName' => 'addProducts'],
            ['$type' => 'remove', '$methodName' => 'removeProducts']
        ];
    }
}
