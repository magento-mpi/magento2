<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

use Magento\Catalog\Model\Category;
use Magento\TestFramework\Helper\ObjectManager;

class ProductUrlRewriteGeneratorTest extends \PHPUnit_Framework_TestCase
{
    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $canonicalUrlRewriteGenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $currentUrlRewritesRegenerator;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $categoriesUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator */
    protected $productUrlRewriteGenerator;

    /** @var \Magento\CatalogUrlRewrite\Service\V1\StoreViewService|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeViewService;

    /** @var \Magento\Catalog\Model\Product|\PHPUnit_Framework_MockObject_MockObject */
    protected $product;

    protected function setUp()
    {
        $this->product = $this->getMock('Magento\Catalog\Model\Product', [], [], '', false);
        $categoriesCollection = $this->getMockBuilder('Magento\Catalog\Model\Resource\Category\Collection')
            ->disableOriginalConstructor()->getMock();
        $this->product->expects($this->any())->method('getCategoryCollection')
            ->will($this->returnValue($categoriesCollection));
        $categoriesCollection->expects($this->exactly(2))->method('addAttributeToSelect')
            ->will($this->returnSelf());
        $this->currentUrlRewritesRegenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\Product\CurrentUrlRewritesRegenerator'
        )->disableOriginalConstructor()->getMock();
        $this->canonicalUrlRewriteGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\Product\CanonicalUrlRewriteGenerator'
        )->disableOriginalConstructor()->getMock();
        $this->categoriesUrlRewriteGenerator = $this->getMockBuilder(
            'Magento\CatalogUrlRewrite\Model\Product\CategoriesUrlRewriteGenerator'
        )->disableOriginalConstructor()->getMock();
        $objectRegistry = $this->getMockBuilder('Magento\CatalogUrlRewrite\Model\ObjectRegistry')
            ->disableOriginalConstructor()->getMock();
        $objectRegistryFactory = $this->getMockBuilder('Magento\CatalogUrlRewrite\Model\ObjectRegistryFactory')
            ->disableOriginalConstructor()->setMethods(['create'])->getMock();
        $objectRegistryFactory->expects($this->any())->method('create')
            ->with(['entities' => $categoriesCollection])
            ->will($this->returnValue($objectRegistry));
        $this->storeViewService = $this->getMockBuilder('Magento\CatalogUrlRewrite\Service\V1\StoreViewService')
            ->disableOriginalConstructor()->getMock();

        $this->productUrlRewriteGenerator = (new ObjectManager($this))->getObject(
            'Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator',
            [
                'canonicalUrlRewriteGenerator' => $this->canonicalUrlRewriteGenerator,
                'categoriesUrlRewriteGenerator' => $this->categoriesUrlRewriteGenerator,
                'currentUrlRewritesRegenerator' => $this->currentUrlRewritesRegenerator,
                'objectRegistryFactory' => $objectRegistryFactory,
                'storeViewService' => $this->storeViewService,
            ]
        );
    }

    public function testGenerationForGlobalScope()
    {
        $this->product->expects($this->any())->method('getStoreId')->will($this->returnValue(null));
        $this->product->expects($this->any())->method('getStoreIds')->will($this->returnValue([1]));
        $this->storeViewService->expects($this->once())->method('doesEntityHaveOverriddenUrlKeyForStore')
            ->will($this->returnValue(false));
        $this->canonicalUrlRewriteGenerator->expects($this->any())->method('generate')
            ->will($this->returnValue(['canonical']));
        $this->categoriesUrlRewriteGenerator->expects($this->any())->method('generate')
            ->will($this->returnValue(['categories']));
        $this->currentUrlRewritesRegenerator->expects($this->any())->method('generate')
            ->will($this->returnValue(['current']));

        $this->assertEquals(
            ['canonical', 'categories', 'current'],
            $this->productUrlRewriteGenerator->generate($this->product)
        );
    }

    public function testGenerationForSpecificStore()
    {
        $this->product->expects($this->any())->method('getStoreId')->will($this->returnValue(1));
        $this->product->expects($this->never())->method('getStoreIds');
        $this->canonicalUrlRewriteGenerator->expects($this->any())->method('generate')
            ->will($this->returnValue(['canonical']));
        $this->categoriesUrlRewriteGenerator->expects($this->any())->method('generate')
            ->will($this->returnValue([]));
        $this->currentUrlRewritesRegenerator->expects($this->any())->method('generate')
            ->will($this->returnValue([]));

        $this->assertEquals(['canonical'], $this->productUrlRewriteGenerator->generate($this->product));
    }

    public function testSkipGenerationForGlobalScope()
    {
        $this->product->expects($this->any())->method('getStoreIds')->will($this->returnValue([1, 2]));
        $this->storeViewService->expects($this->exactly(2))->method('doesEntityHaveOverriddenUrlKeyForStore')
            ->will($this->returnValue(true));

        $this->assertEquals([], $this->productUrlRewriteGenerator->generate($this->product));
    }
}
