<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\OptionProvider;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class CurrentUrlRewritesRegeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator */
    protected $currentUrlRewritesRegenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $filter;

    /** @var \Magento\UrlRewrite\Model\UrlFinderInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlFinder;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator|\PHPUnit_Framework_MockObject_MockObject */
    protected $productUrlPathGenerator;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $category;

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistry|\PHPUnit_Framework_MockObject_MockObject */
    protected $objectRegistry;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlRewriteBuilder;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlRewrite;

    protected function setUp()
    {
        $this->urlRewriteBuilder = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder')
            ->disableOriginalConstructor()->getMock();
        $this->urlRewrite = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
            ->disableOriginalConstructor()->getMock();
        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()->getMock();
        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()->getMock();
        $this->objectRegistry = $this->getMockBuilder('\Magento\CatalogUrlRewrite\Model\ObjectRegistry')
            ->disableOriginalConstructor()->getMock();
        $this->filter = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\Filter')
            ->disableOriginalConstructor()->getMock();
        $this->filter->expects($this->any())->method('setStoreId')->will($this->returnSelf());
        $this->filter->expects($this->any())->method('setEntityId')->will($this->returnSelf());
        $this->urlFinder= $this->getMockBuilder('Magento\UrlRewrite\Model\UrlFinderInterface')
            ->disableOriginalConstructor()->getMock();
        $this->productUrlPathGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator'
        )->disableOriginalConstructor()->getMock();
        $this->currentUrlRewritesRegenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator',
            [
                'urlFinder' => $this->urlFinder,
                'productUrlPathGenerator' => $this->productUrlPathGenerator,
                'urlRewriteBuilder' => $this->urlRewriteBuilder
            ]
        );
    }

    public function testIsAutogeneratedWithoutSaveRewriteHistory()
    {
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([[UrlRewrite::IS_AUTOGENERATED => 1]])));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(false));

        $this->assertEquals(
            [],
            $this->currentUrlRewritesRegenerator->generate('store_id', $this->product, $this->objectRegistry)
        );
    }

    public function testSkipGenerationForAutogenerated()
    {
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [UrlRewrite::IS_AUTOGENERATED => 1, UrlRewrite::REQUEST_PATH => 'same-path']
            ])));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('same-path'));

        $this->assertEquals(
            [],
            $this->currentUrlRewritesRegenerator->generate('store_id', $this->product, $this->objectRegistry)
        );
    }

    public function testIsAutogeneratedWithoutCategory()
    {
        $requestPath = 'autogenerated.html';
        $targetPath = 'some-path.html';
        $storeId = 2;
        $productId = 12;
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => $requestPath,
                    UrlRewrite::TARGET_PATH => 'custom-target-path',
                    UrlRewrite::STORE_ID => $storeId,
                    UrlRewrite::IS_AUTOGENERATED => 1,
                    UrlRewrite::METADATA => [],
                ],
            ])));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue($targetPath));

        $this->prepareUrlRewriteMock($storeId, $productId, $requestPath, $targetPath, OptionProvider::PERMANENT, []);

        $this->assertEquals(
            [$this->urlRewrite],
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->product, $this->objectRegistry)
        );
    }

    public function testIsAutogeneratedWithCategory()
    {
        $productId = 12;
        $categoryId = 2;
        $requestPath = 'autogenerated.html';
        $targetPath = 'simple-product.html';
        $storeId = 2;
        $metadata = ['category_id' => 2, 'some_another_data' => 1];
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => $requestPath,
                    UrlRewrite::TARGET_PATH => 'some-path.html',
                    UrlRewrite::STORE_ID => $storeId,
                    UrlRewrite::IS_AUTOGENERATED => 1,
                    UrlRewrite::METADATA => $metadata,
                ],
            ])));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue($targetPath));
        $this->objectRegistry->expects($this->once())->method('get')->will($this->returnValue($this->category));
        $this->prepareUrlRewriteMock(
            $storeId,
            $productId,
            $requestPath,
            $targetPath,
            OptionProvider::PERMANENT,
            $metadata
        );

        $this->assertEquals(
            [$this->urlRewrite],
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->product, $this->objectRegistry)
        );
    }

    public function testSkipGenerationForCustom()
    {
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::REQUEST_PATH => 'same-path',
                    UrlRewrite::REDIRECT_TYPE => 1
                ]
            ])));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('same-path'));

        $this->assertEquals(
            [],
            $this->currentUrlRewritesRegenerator->generate('store_id', $this->product, $this->objectRegistry)
        );
    }

    /**
     * @return array
     */
    public function generateForCustomDataProvider()
    {
        return [
            [
                [[
                    UrlRewrite::REQUEST_PATH => 'generate-for-custom-by-user.html',
                    UrlRewrite::TARGET_PATH => 'custom-target-path.html',
                    UrlRewrite::REDIRECT_TYPE => 'some-code',
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::METADATA => ['is_user_generated' => 1],
                ]],
                'store_id', 'product_id', 'generate-for-custom-by-user.html', 'custom-target-path.html', 'some-code',
                ['is_user_generated' => 1]
            ],
            [
                [[
                    UrlRewrite::REQUEST_PATH => 'generate-for-custom-by-user.html',
                    UrlRewrite::TARGET_PATH => 'custom-target-path.html',
                    UrlRewrite::REDIRECT_TYPE => 0,
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::METADATA => ['some_data' => '123'],
                ]],
                'store_id', 'product_id', 'generate-for-custom-by-user.html', 'custom-target-path.html', 0,
                ['some_data' => '123']
            ],
        ];
    }

    /**
     * @dataProvider generateForCustomDataProvider
     */
    public function testGenerationForCustom(
        $currentRewrites,
        $storeId,
        $productId,
        $requestPath,
        $targetPath,
        $redirectCode,
        $metadata
    ) {
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks($currentRewrites)));
        $this->productUrlPathGenerator->expects($this->any())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('generated-target-path.html'));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue('product_id'));
        $this->urlRewriteBuilder->expects($this->once())->method('setDescription')->with('')->will($this->returnSelf());
        $this->prepareUrlRewriteMock($storeId, $productId, $requestPath, $targetPath, $redirectCode, $metadata);
        $this->assertEquals(
            [$this->urlRewrite],
            $this->currentUrlRewritesRegenerator->generate('store_id', $this->product, $this->objectRegistry)
        );
    }

    public function testGenerationForCustomWithPathGeneration()
    {
        $storeId = 12;
        $productId = 123;
        $requestPath = 'generate-for-custom-without-redirect-type.html';
        $targetPath = 'generated-target-path.html';
        $description = 'description';
        $this->urlFinder->expects($this->once())->method('findAllByData')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => $requestPath,
                    UrlRewrite::TARGET_PATH => 'custom-target-path.html',
                    UrlRewrite::REDIRECT_TYPE => 'code',
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::DESCRIPTION => $description,
                    UrlRewrite::METADATA => [],
                ]
            ])));
        $this->productUrlPathGenerator->expects($this->any())->method('getUrlPathWithSuffix')
            ->will($this->returnValue($targetPath));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->urlRewriteBuilder->expects($this->once())->method('setDescription')->with($description)
            ->will($this->returnSelf());
        $this->prepareUrlRewriteMock($storeId, $productId, $requestPath, $targetPath, 'code', []);

        $this->assertEquals(
            [$this->urlRewrite],
            $this->currentUrlRewritesRegenerator->generate($storeId, $this->product, $this->objectRegistry)
        );
    }

    /**
     * @param array $currentRewrites
     * @return array
     */
    protected function getCurrentRewritesMocks($currentRewrites)
    {
        $rewrites = [];
        foreach ($currentRewrites as $urlRewrite) {
            /** @var \PHPUnit_Framework_MockObject_MockObject */
            $url = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite')
                ->disableOriginalConstructor()->getMock();
            foreach ($urlRewrite as $key => $value) {
                $url->expects($this->any())
                    ->method('get' . str_replace(' ', '', ucwords(str_replace('_', ' ', $key))))
                    ->will($this->returnValue($value));
            }
            $rewrites[] = $url;
        }
        return $rewrites;
    }

    /**
     * @param mixed $storeId
     * @param mixed $productId
     * @param mixed $requestPath
     * @param mixed $targetPath
     * @param mixed $redirectType
     * @param mixed $metadata
     */
    protected function prepareUrlRewriteMock($storeId, $productId, $requestPath, $targetPath, $redirectType, $metadata)
    {
        $this->urlRewriteBuilder->expects($this->any())->method('setStoreId')->with($storeId)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setEntityId')->with($productId)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setEntityType')
            ->with(ProductUrlRewriteGenerator::ENTITY_TYPE)->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setRequestPath')->with($requestPath)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setTargetPath')->with($targetPath)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setIsAutogenerated')->with(0)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setRedirectType')->with($redirectType)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setMetadata')->with($metadata)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('create')->will($this->returnValue($this->urlRewrite));
    }
}
