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

class CurrentUrlRewriteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewriteGenerator */
    protected $currentUrlRewriteGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $filter;

    /** @var \Magento\UrlRewrite\Service\V1\UrlMatcherInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $urlMatcher;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator|\PHPUnit_Framework_MockObject_MockObject */
    protected $productUrlPathGenerator;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    /** @var \Magento\Catalog\Model\Category|\PHPUnit_Framework_MockObject_MockObject */
    protected $category;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistry|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryRegistry;

    protected function setUp()
    {
        $this->product = $this->getMockBuilder('Magento\Catalog\Model\Product')
            ->disableOriginalConstructor()->getMock();
        $this->category = $this->getMockBuilder('Magento\Catalog\Model\Category')
            ->disableOriginalConstructor()->getMock();
        $this->categoryRegistry = $this->getMockBuilder('\Magento\CatalogUrlRewrite\Model\CategoryRegistry')
            ->disableOriginalConstructor()->getMock();
        $this->filter = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\Filter')
            ->disableOriginalConstructor()->getMock();
        $this->filter->expects($this->any())->method('setStoreId')->will($this->returnSelf());
        $this->filter->expects($this->any())->method('setEntityId')->will($this->returnSelf());
        $filterFactory = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\FilterFactory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $filterFactory->expects($this->any())->method('create')->will($this->returnValue($this->filter));
        $this->urlMatcher = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\UrlMatcherInterface')
            ->disableOriginalConstructor()->getMock();
        $this->productUrlPathGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator'
        )->disableOriginalConstructor()->getMock();
        $converter = $this->getMockBuilder('Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter')
            ->disableOriginalConstructor()->getMock();
        $converter->expects($this->any())->method('convertArrayToObject')->will($this->returnArgument(0));
        $this->currentUrlRewriteGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewriteGenerator',
            [
                'filterFactory' => $filterFactory,
                'urlMatcher' => $this->urlMatcher,
                'productUrlPathGenerator' => $this->productUrlPathGenerator,
                'converter' => $converter
            ]
        );
    }

    public function testIsAutogeneratedWithoutSaveRewriteHistory()
    {
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks([[UrlRewrite::IS_AUTOGENERATED => 1]])));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(false));

        $this->assertEquals(
            [],
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
        );
    }

    public function testSkipGenerationForAutogenerated()
    {
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [UrlRewrite::IS_AUTOGENERATED => 1, UrlRewrite::REQUEST_PATH => 'same-path']
            ])));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('same-path'));

        $this->assertEquals(
            [],
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
        );
    }

    public function testIsAutogeneratedWithoutCategory()
    {
        $productId = 12;
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => 'autogenerated.html',
                    UrlRewrite::TARGET_PATH => 'some-path.html',
                    UrlRewrite::STORE_ID => 2,
                    UrlRewrite::IS_AUTOGENERATED => 1,
                    UrlRewrite::METADATA => null,
                ],
            ])));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('simple-product.html'));

        $this->assertEquals(
            [
                [
                    'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE,
                    'entity_id' => $productId,
                    'store_id' => 2,
                    'request_path' => 'autogenerated.html',
                    'target_path' => 'simple-product.html',
                    'redirect_type' => OptionProvider::PERMANENT,
                    'is_autogenerated' => false,
                    'metadata' => null
                ]
            ],
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
        );
    }

    public function testIsAutogeneratedWithCategory()
    {
        $productId = 12;
        $categoryId = 2;
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => 'autogenerated.html',
                    UrlRewrite::TARGET_PATH => 'some-path.html',
                    UrlRewrite::STORE_ID => 2,
                    UrlRewrite::IS_AUTOGENERATED => 1,
                    UrlRewrite::METADATA => ['category_id' => $categoryId, 'some_another_data' => 1],
                ],
            ])));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->product->expects($this->once())->method('getData')->with('save_rewrites_history')
            ->will($this->returnValue(true));
        $this->productUrlPathGenerator->expects($this->once())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('simple-product.html'));
        $this->categoryRegistry->expects($this->once())->method('get')->will($this->returnValue($this->category));
        $this->category->expects($this->once())->method('getId')->will($this->returnValue($categoryId));

        $this->assertEquals(
            [
                [
                    'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE,
                    'entity_id' => $productId,
                    'store_id' => 2,
                    'request_path' => 'autogenerated.html',
                    'target_path' => 'simple-product.html',
                    'redirect_type' => OptionProvider::PERMANENT,
                    'is_autogenerated' => false,
                    'metadata' => serialize(['category_id' => 2, 'some_another_data' => 1]),
                ]
            ],
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
        );
    }

    public function testSkipGenerationForCustom()
    {
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
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
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
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
                    UrlRewrite::REDIRECT_TYPE => 'some-type',
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::METADATA => ['is_user_generated' => 1],
                ]],
                [[
                    'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE,
                    'entity_id' => 'product_id',
                    'store_id' => 'store_id',
                    'request_path' => 'generate-for-custom-by-user.html',
                    'target_path' => 'custom-target-path.html',
                    'redirect_type' => 'some-type',
                    'is_autogenerated' => false,
                    'metadata' => serialize(['is_user_generated' => 1]),
                ]],
            ],
            [
                [[
                    UrlRewrite::REQUEST_PATH => 'generate-for-custom-by-user.html',
                    UrlRewrite::TARGET_PATH => 'custom-target-path.html',
                    UrlRewrite::REDIRECT_TYPE => 0,
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::METADATA => ['is_user_generated' => false],
                ]],
                [[
                    'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE,
                    'entity_id' => 'product_id',
                    'store_id' => 'store_id',
                    'request_path' => 'generate-for-custom-by-user.html',
                    'target_path' => 'custom-target-path.html',
                    'redirect_type' => 0,
                    'is_autogenerated' => false,
                    'metadata' => serialize(['is_user_generated' => false]),
                ]],
            ],
        ];
    }

    /**
     * @dataProvider generateForCustomDataProvider
     * @param array $currentRewrites
     * @param array $result
     */
    public function testGenerationForCustom($currentRewrites, $result)
    {
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks($currentRewrites)));
        $this->productUrlPathGenerator->expects($this->any())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('generated-target-path.html'));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue('product_id'));
        $this->assertEquals(
            $result,
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
        );
    }

    public function testGenerationForCustomWithPathGeneration()
    {
        $this->urlMatcher->expects($this->once())->method('findAllByFilter')
            ->will($this->returnValue($this->getCurrentRewritesMocks([
                [
                    UrlRewrite::REQUEST_PATH => 'generate-for-custom-without-redirect-type.html',
                    UrlRewrite::TARGET_PATH => 'custom-target-path.html',
                    UrlRewrite::REDIRECT_TYPE => 301,
                    UrlRewrite::IS_AUTOGENERATED => 0,
                    UrlRewrite::METADATA => ['is_user_generated' => false],
                ]
            ])));
        $this->productUrlPathGenerator->expects($this->any())->method('getUrlPathWithSuffix')
            ->will($this->returnValue('generated-target-path.html'));
        $this->product->expects($this->any())->method('getId')->will($this->returnValue('product_id'));
        $this->assertEquals(
            [
                [
                    'entity_type' => ProductUrlRewriteGenerator::ENTITY_TYPE,
                    'entity_id' => 'product_id',
                    'store_id' => 'store_id',
                    'request_path' => 'generate-for-custom-without-redirect-type.html',
                    'target_path' => 'generated-target-path.html',
                    'redirect_type' => 301,
                    'is_autogenerated' => false,
                    'metadata' => serialize(['is_user_generated' => false]),
                ]
            ]
            ,
            $this->currentUrlRewriteGenerator->generate('store_id', $this->product, $this->categoryRegistry)
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
}
