<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\TestFramework\Helper\ObjectManager;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;

class CanonicalUrlRewriteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator */
    protected $canonicalUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator|\PHPUnit_Framework_MockObject_MockObject */
    protected $productUrlPathGenerator;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryRegistry|\PHPUnit_Framework_MockObject_MockObject */
    protected $categoryRegistry;

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
        $this->categoryRegistry = $this->getMockBuilder('\Magento\CatalogUrlRewrite\Model\CategoryRegistry')
            ->disableOriginalConstructor()->getMock();
        $this->productUrlPathGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator'
        )->disableOriginalConstructor()->getMock();
        $this->canonicalUrlRewriteGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator',
            [
                'productUrlPathGenerator' => $this->productUrlPathGenerator,
                'urlRewriteBuilder' => $this->urlRewriteBuilder
            ]
        );
    }

    public function testGenerate()
    {
        $urlPath = 'simple-product.html';
        $storeId = 10;
        $productId = 'product_id';
        $canonicalUrlPath = 'catalog/product/view/id/' . $productId;

        $this->product->expects($this->any())->method('getId')->will($this->returnValue($productId));
        $this->product->expects($this->any())->method('getStoreId')->will($this->returnValue($storeId));
        $this->productUrlPathGenerator->expects($this->any())->method('getUrlPathWithSuffix')
            ->will($this->returnValue($urlPath));
        $this->productUrlPathGenerator->expects($this->any())->method('getCanonicalUrlPath')
            ->will($this->returnValue($canonicalUrlPath));
        $this->categoryRegistry->expects($this->any())->method('getList')->will($this->returnValue([]));

        $this->urlRewriteBuilder->expects($this->any())->method('setStoreId')->with($storeId)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setEntityId')->with($productId)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setEntityType')
            ->with(ProductUrlRewriteGenerator::ENTITY_TYPE)->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setRequestPath')->with($urlPath)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('setTargetPath')->with($canonicalUrlPath)
            ->will($this->returnSelf());
        $this->urlRewriteBuilder->expects($this->any())->method('create')->will($this->returnValue($this->urlRewrite));
        $this->assertEquals(
            [
                $this->urlRewrite,
            ],
            $this->canonicalUrlRewriteGenerator->generate($storeId, $this->product)
        );
    }
}
