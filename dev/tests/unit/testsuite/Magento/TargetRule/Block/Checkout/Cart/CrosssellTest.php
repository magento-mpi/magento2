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

    /** @var \Magento\TargetRule\Helper\Data|\PHPUnit_Framework_MockObject_MockObject */
    protected $targetRuleHelper;

    /** @var \PHPUnit_Framework_MockObject_MockObject */
    protected $linkFactory;

    /** @var \Magento\Store\Model\StoreManagerInterface|\PHPUnit_Framework_MockObject_MockObject */
    protected $storeManager;

    protected function setUp()
    {
        $this->storeManager = $this->getMock('Magento\Store\Model\StoreManagerInterface');
        $catalogConfig = $this->getMock('Magento\Catalog\Model\Config', [], [], '', false);
        $context = $this->getMock('Magento\Catalog\Block\Product\Context', [], [], '', false);
        $context->expects($this->any())->method('getStoreManager')->willReturn($this->storeManager);
        $context->expects($this->any())->method('getCatalogConfig')->willReturn($catalogConfig);
        $index = $this->getMock('Magento\TargetRule\Model\Resource\Index', [], [], '', false);
        $this->targetRuleHelper = $this->getMock('Magento\TargetRule\Helper\Data', [], [], '', false);
        $collectionFactory = $this->getMock('Magento\Catalog\Model\Resource\Product\CollectionFactory');
        $visibility = $this->getMock('Magento\Catalog\Model\Product\Visibility', [], [], '', false);
        $status = $this->getMock('Magento\CatalogInventory\Model\Stock\Status', [], [], '', false);
        $session = $this->getMock('Magento\Checkout\Model\Session', [], [], '', false);
        $this->linkFactory = $this->getMock('Magento\Catalog\Model\Product\LinkFactory', ['create']);
        $productFactory = $this->getMock('Magento\Catalog\Model\ProductFactory');
        $indexFactory = $this->getMock('Magento\TargetRule\Model\IndexFactory');
        $config = $this->getMock('Magento\Catalog\Model\ProductTypes\ConfigInterface');

        $this->crosssell = (new ObjectManager($this))->getObject(
            'Magento\TargetRule\Block\Checkout\Cart\Crosssell',
            [
                'context' => $context,
                'index' => $index,
                'targetRuleData' => $this->targetRuleHelper,
                'productCollectionFactory' => $collectionFactory,
                'visibility' => $visibility,
                'status' => $status,
                'session' => $session,
                'productLinkFactory' => $this->linkFactory,
                'productFactory' => $productFactory,
                'indexFactory' => $indexFactory,
                'productTypeConfig' => $config
            ]
        );
    }

    /**
     * @covers Magento\TargetRule\Block\Checkout\Cart\Crosssell::_getTargetLinkCollection
     */
    public function testGetTargetLinkCollection()
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

        $this->assertSame($productCollection, $this->crosssell->getLinkCollection());
    }
}
