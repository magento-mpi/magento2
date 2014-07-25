<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\TestFramework\Helper\ObjectManager as ObjectManagerHelper;
use Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product;

class GeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\Product\Generator */
    protected $generator;

    /** @var ObjectManagerHelper */
    protected $objectManagerHelper;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $productMock;

    /** @var \Magento\Catalog\Model\Resource\Category\Collection|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoriesCollectionMock;

    /** @var \Magento\Catalog\Helper\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $catalogProductHelperMock;

    /** @var \Magento\UrlRewrite\Service\V1\Storage\Data\Converter|\PHPUnit_Framework_MockObject_MockObject */
    protected $converterMock;

    /** @var \Magento\UrlRewrite\Service\V1\StorageInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storageMock;

    /** @var \Magento\UrlRewrite\Service\V1\Storage\Data\FilterFactory|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterFactoryMock;

    /** @var \Magento\UrlRewrite\Service\V1\Storage\Data\Filter|\PHPUnit_Framework_MockObject_MockObject */
    protected $filterMock;

    protected function setUp()
    {
        return; // @TODO: UrlRewrite: Actualize test according to implementation changes.
        $this->categoriesCollectionMock = $this->getMock('Magento\Catalog\Model\Resource\Category\Collection', [], [],
            '', false);
        $this->productMock = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $this->catalogProductHelperMock = $this->getMock('Magento\Catalog\Helper\Product', [], [], '', false);
        $this->converterMock = $this->getMock('Magento\UrlRewrite\Service\V1\Storage\Data\Converter', [], [], '',
            false);

        $this->storageMock = $this->getMock('Magento\UrlRewrite\Service\V1\StorageInterface');
        $this->filterMock = $this->getMock('Magento\UrlRewrite\Service\V1\Storage\Data\Filter');
        $this->filterFactoryMock = $this->getMock('Magento\UrlRewrite\Service\V1\Storage\Data\FilterFactory',
            ['create']);
        $this->filterFactoryMock->expects($this->once())->method('create')->will($this->returnValue($this->filterMock));

        $this->objectManagerHelper = new ObjectManagerHelper($this);
        $this->generator = $this->objectManagerHelper->getObject(
            'Magento\CatalogUrlRewrite\Model\Product\Generator',
            [
                'product' => $this->productMock,
                'productHelper' => $this->catalogProductHelperMock,
                'converter' => $this->converterMock,
                'storage' => $this->storageMock,
                'filterFactory' => $this->filterFactoryMock
            ]
        );
    }

    /**
     * @return array
     */
    public function generateDataProvider()
    {
        return [
            [1, 'banana', 'apple', '.html', 1, null, null, [
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'banana.html',
                    'target_path' => 'catalog/product/view/id/1',
                    'redirect_type' => '',
                ],
            ]],
            [1, 'banana', 'apple', '.html', 1, null, null, [
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'banana.html',
                    'target_path' => 'catalog/product/view/id/1',
                    'redirect_type' => '',
                ],
            ]],
            [1, 'banana', 'apple', '.html', 1, 1, 'category-one', [
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'banana.html',
                    'target_path' => 'catalog/product/view/id/1',
                    'redirect_type' => '',
                ],
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'category-one/banana.html',
                    'target_path' => 'catalog/product/view/id/1/category/1',
                    'redirect_type' => '',
                ],
            ]],
        ];
    }

    /**
     * @dataProvider generateDataProvider
     */
    public function testGeneratePerStore(
        $storeId,
        $newUrlKey,
        $oldUrkKey,
        $urlSuffix,
        $productId,
        $categoryId,
        $categoryUrlPath,
        $results
    ) {
        $this->markTestIncomplete('@TODO: UrlRewrite: Actualize test according to implementation changes.');
        $this->productMock->expects($this->any())->method('getData')->will($this->returnValueMap(
            [
                ['save_rewrites_history', null, false],
                ['url_key', null, $newUrlKey],
            ]
        ));

        $this->productMock->expects($this->any())->method('getOrigData')->with('url_key')
            ->will($this->returnValue($oldUrkKey));
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->productMock->expects($this->any())->method('getCategoryCollection')
            ->will($this->returnValue($this->categoriesCollectionMock));

        $this->catalogProductHelperMock->expects($this->any())->method('getProductUrlSuffix')->with($storeId)
            ->will($this->returnValue($urlSuffix));
        $this->initCategories($categoryId, $categoryUrlPath);

        $previousUrls = [];
        $this->storageMock->expects($this->once())->method('findAllByFilter')->with($this->filterMock)
            ->will($this->returnValue($previousUrls));

        $this->assertEquals($results, $this->generator->generatePerStore($storeId));
    }

    /**
     * @param null|int $categoryId
     * @param null|int $categoryUrlPath
     */
    protected function initCategories($categoryId = null, $categoryUrlPath = null)
    {
        if ($categoryId) {
            $category1Mock = $this->getMock('Magento\Catalog\Model\Category', [], [], '', false);
            $category1Mock->expects($this->once())->method('getId')->will($this->returnValue($categoryId));
            $category1Mock->expects($this->once())->method('getUrlPath')->will($this->returnValue($categoryUrlPath));
            $categories = [$category1Mock];
        } else {
            $categories = [];
        }
        $categoriesIterator = new \ArrayIterator($categories);
        $this->categoriesCollectionMock->expects($this->exactly(2))->method('addAttributeToSelect')
            ->will($this->returnSelf());
        $this->categoriesCollectionMock->expects($this->once())->method('getIterator')
            ->will($this->returnValue($categoriesIterator));
    }

    /**
     * @return array
     */
    public function generateWithRedirectDataProvider()
    {
        return [
            [1, 'banana', 'apple', '.html', 1, 'apple.html',
                'some-target-path', ProductUrlGenerator::ENTITY_TYPE_PRODUCT, [
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'banana.html',
                    'target_path' => 'catalog/product/view/id/1',
                    'redirect_type' => '',
                ],
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'apple.html',
                    'target_path' => 'banana.html',
                    'redirect_type' => 'RP',
                ]
            ],
            ],
            [1, 'banana', 'apple', '.html', 1,
                'some-request-path', 'apple.html', ProductUrlGenerator::ENTITY_TYPE_PRODUCT, [
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'banana.html',
                    'target_path' => 'catalog/product/view/id/1',
                    'redirect_type' => '',
                ],
                [
                    'entity_type' => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
                    'entity_id' => 1,
                    'store_id' => 1,
                    'request_path' => 'some-request-path',
                    'target_path' => 'banana.html',
                    'redirect_type' => 'RP',
                ],
            ],
            ]];
    }

    /**
     * @dataProvider generateWithRedirectDataProvider
     */
    public function testGeneratePerStoreWithRedirect(
        $storeId,
        $newUrlKey,
        $oldUrkKey,
        $urlSuffix,
        $productId,
        $oldRequestPath,
        $oldTargetPath,
        $oldEntityType,
        $results
    ) {
        $this->markTestIncomplete('@TODO: UrlRewrite: Actualize test according to implementation changes.');
        $this->productMock->expects($this->any())->method('getData')->will($this->returnValueMap(
            [
                ['save_rewrites_history', null, false],
                ['url_key', null, $newUrlKey],
            ]
        ));
        $this->productMock->expects($this->any())->method('getOrigData')->with('url_key')
            ->will($this->returnValue($oldUrkKey));
        $this->productMock->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->productMock->expects($this->any())->method('getCategoryCollection')
            ->will($this->returnValue($this->categoriesCollectionMock));

        $this->initCategories(null, null);

        $this->catalogProductHelperMock->expects($this->any())->method('getProductUrlSuffix')->with($storeId)
            ->will($this->returnValue($urlSuffix));


        $oldProductDataMock = $this->getMock('Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product', [], [], '',
            false);
        $oldProductDataMock->expects($this->any())->method('getEntityType')->will($this->returnValue($oldEntityType));
        $oldProductDataMock->expects($this->any())->method('getRequestPath')->will($this->returnValue($oldRequestPath));
        $oldProductDataMock->expects($this->any())->method('getTargetPath')->will($this->returnValue($oldTargetPath));

        $previousUrls = [$oldProductDataMock];
        $this->storageMock->expects($this->once())->method('findAllByFilter')->with($this->filterMock)
            ->will($this->returnValue($previousUrls));

        $urls = $this->generator->generatePerStore($storeId);
        $this->assertEquals($results, $urls);
    }
}
