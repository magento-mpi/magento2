<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license {license_link}
 */
namespace Magento\CatalogInventory\Model\Indexer\Stock\Action;

/**
 * Class RowTest
 */
class RowTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Processor
     */
    protected $_processor;

    protected function setUp()
    {
        $this->_processor = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogInventory\Model\Indexer\Stock\Processor'
        );
    }

    /**
     * @magentoDataFixture Magento/Catalog/_files/product_simple.php
     */
    public function testProductUpdate()
    {
        $categoryFactory = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\Catalog\Model\CategoryFactory'
        );
        /** @var \Magento\Catalog\Block\Product\ListProduct $listProduct */
        $listProduct = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\Catalog\Block\Product\ListProduct'
        );

        /** @var \Magento\CatalogInventory\Api\Data\StockItemInterfaceBuilder $stockItemBuilder */
        $stockItemBuilder = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\CatalogInventory\Api\Data\StockItemInterfaceBuilder'
        );

        /** @var \Magento\CatalogInventory\Api\StockRegistryInterface $stockRegistry */
        $stockRegistry = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            'Magento\CatalogInventory\Api\StockRegistryInterface'
        );
        /** @var \Magento\CatalogInventory\Api\StockItemRepositoryInterface $stockItemRepository */
        $stockItemRepository = \Magento\TestFramework\Helper\Bootstrap::getObjectManager()->create(
            '\Magento\CatalogInventory\Api\StockItemRepositoryInterface'
        );

        $this->_processor->getIndexer()->setScheduled(false);
        $this->assertFalse($this->_processor->getIndexer()->isScheduled());

        $stockItem = $stockRegistry->getStockItem(1, 1);

        $this->assertNotNull($stockItem->getId());

        $stockItemData = [
            'qty' => $stockItem->getQty() + 11
        ];

        // todo fix builder
        $id = $stockItem->getId();
        $stockItemBuilder = $stockItemBuilder->mergeDataObjectWithArray($stockItem, $stockItemData);
        $stockItemBuilder->setId($id);
        $stockItemSave = $stockItemBuilder->create();
        $stockItemSave->setItemId($id);
        $stockItemRepository->save($stockItemSave);

        $category = $categoryFactory->create()->load(2);
        $layer = $listProduct->getLayer();
        $layer->setCurrentCategory($category);
        $productCollection = $layer->getProductCollection();
        $productCollection->joinField(
            'qty',
            'cataloginventory_stock_status',
            'qty',
            'product_id=entity_id',
            '{{table}}.stock_id=1',
            'left'
        );

        $this->assertEquals(1, $productCollection->count());
        /** @var $product \Magento\Catalog\Model\Product */
        foreach ($productCollection as $product) {
            $this->assertEquals('Simple Product', $product->getName());
            $this->assertEquals('Short description', $product->getShortDescription());
            $this->assertEquals(111, $product->getQty());
        }
    }
}
