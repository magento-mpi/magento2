<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Block\Checkout\Cart;

use Magento\TestFramework\Helper\ObjectManager;

class CrosssellTest extends \PHPUnit_Framework_TestCase
{
    /** @var \Magento\TargetRule\Block\Checkout\Cart\Crosssell */
    protected $crosssell;

    /** @var \Magento\Catalog\Block\Product\Context|\PHPUnit_Framework_MockObject_MockObject */
    protected $context;

    /** @var \Magento\TargetRule\Model\Resource\Index|\PHPUnit_Framework_MockObject_MockObject */
    protected $index;

    /** @var \Magento\TargetRule\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $targetRuleHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $collectionFactory;

    /** @var \Magento\Catalog\Model\Product\Visibility|\PHPUnit_Framework_MockObject_MockObject */
    protected $visibility;

    /** @var \Magento\CatalogInventory\Model\Stock\Status|\PHPUnit_Framework_MockObject_MockObject */
    protected $status;

    /** @var \Magento\Checkout\Model\Session|\PHPUnit_Framework_MockObject_MockObject */
    protected $session;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $linkFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $productFactory;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $indexFactory;

    /** @var \Magento\Catalog\Model\ProductTypes\ConfigInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $config;

    /** @var \Magento\Framework\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    /** @var \Magento\Catalog\Model\Config|\PHPUnit_Framework_MockObject_MockObject */
    protected $catalogConfig;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('Magento\Framework\StoreManagerInterface');
        $this->catalogConfig = $this->getMock('Magento\Catalog\Model\Config', [], [], '', false);
        $this->context = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false);
        $this->context->expects($this->any())->method('getStoreManager')->willReturn($this->storeManager);
        $this->context->expects($this->any())->method('getCatalogConfig')->willReturn($this->catalogConfig);
        $this->index = $this->getMock('Magento\TargetRule\Model\Resource\Index', [], [], '', false);
        $this->targetRuleHelper = $this->getMock('Magento\TargetRule\Helper\Data', [], [], '', false);
        $this->collectionFactory = $this->getMock('Magento\Catalog\Model\Resource\Product\CollectionFactory');
        $this->visibility = $this->getMock('Magento\Catalog\Model\Product\Visibility', [], [], '', false);
        $this->status = $this->getMock('Magento\CatalogInventory\Model\Stock\Status', [], [], '', false);
        $this->session = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->linkFactory = $this->getMock('Magento\Catalog\Model\Product\LinkFactory', ['create']);
        $this->productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $this->indexFactory = $this->getMock('Magento\TargetRule\Model\IndexFactory');
        $this->config = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');

        $this->crosssell = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Block\Checkout\Cart\Crosssell',
            [
                'context' => $this->context,
                'index' => $this->index,
                'targetRuleData' => $this->targetRuleHelper,
                'productCollectionFactory' => $this->collectionFactory,
                'visibility' => $this->visibility,
                'status' => $this->status,
                'session' => $this->session,
                'productLinkFactory' => $this->linkFactory,
                'productFactory' => $this->productFactory,
                'indexFactory' => $this->indexFactory,
                'productTypeConfig' => $this->config
            ]
        );
    }

    public function testGetLinkCollection()
    {
        $store = $this->getMock('Magento\Store\Model\Store', [], [], '', false);
        $this->storeManager->expects($this->any())->method('getStore')->willReturn($store);
        $this->targetRuleHelper->expects($this->once())->method('getMaximumNumberOfProduct')
            ->with(\Magento\TargetRule\Model\Rule::CROSS_SELLS);
        $productCollection = $this->getMock(
            'Magento\Catalog\Model\Resource\Product\Link\Product\Collection',
            [],
            [],
            '',
            false
        );
        $productLinkCollection = $this->getMock('Magento\Catalog\Model\Product\Link', [], [], '', false);
        $this->linkFactory->expects($this->once())->method('create')->willReturn($productLinkCollection);
        $productLinkCollection->expects($this->once())->method('useCrossSellLinks')->willReturnSelf();
        $productLinkCollection->expects($this->once())->method('getProductCollection')->willReturn($productCollection);
        $productCollection->expects($this->once())->method('setStoreId')->willReturnSelf();
        $productCollection->expects($this->once())->method('setPageSize')->willReturnSelf();
        $productCollection->expects($this->once())->method('setGroupBy')->willReturnSelf();
        $productCollection->expects($this->once())->method('addMinimalPrice')->willReturnSelf();
        $productCollection->expects($this->once())->method('addFinalPrice')->willReturnSelf();
        $productCollection->expects($this->once())->method('addTaxPercents')->willReturnSelf();
        $productCollection->expects($this->once())->method('addAttributeToSelect')->willReturnSelf();
        $productCollection->expects($this->once())->method('addUrlRewrite')->willReturnSelf();
        $select = $this->getMock('Magento\Framework\DB\Select', [], [], '', false);
        $productCollection->expects($this->once())->method('getSelect')->willReturn($select);

        $this->assertEquals($productCollection, $this->crosssell->getLinkCollection());
    }
}
