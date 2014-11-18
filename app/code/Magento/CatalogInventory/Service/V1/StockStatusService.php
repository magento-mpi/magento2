<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogInventory\Service\V1;

use Magento\CatalogInventory\Model\Stock;
use Magento\CatalogInventory\Model\Stock\Status;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Service related to Product Stock Status
 */
class StockStatusService implements StockStatusServiceInterface
{
    /**
     * @var Status
     */
    protected $stockStatus;

    /**
     * @var \Magento\Store\Model\Resolver\Website
     */
    protected $scopeResolver;

    /**
     * @var \Magento\Catalog\Api\ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var StockItemService
     */
    protected $stockItemService;

    /**
     * @var Data\StockStatusBuilder
     */
    protected $stockStatusBuilder;

    /**
     * @var \Magento\CatalogInventory\Model\Resource\Stock\Status\CollectionFactory
     */
    protected $itemsFactory;

    /**
     * @var Data\LowStockResultBuilder
     */
    protected $lowStockResultBuilder;

    /**
     * @param Status $stockStatus
     * @param StockItemService $stockItemService
     * @param \Magento\Catalog\Api\ProductRepositoryInterface $productRepository
     * @param \Magento\Store\Model\Resolver\Website $scopeResolver
     * @param Data\StockStatusBuilder $stockStatusBuilder
     * @param \Magento\CatalogInventory\Model\Resource\Stock\Status\CollectionFactory $itemsFactory
     * @param Data\LowStockResultBuilder $lowStockResultBuilder
     */
    public function __construct(
        Status $stockStatus,
        \Magento\CatalogInventory\Service\V1\StockItemService $stockItemService,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Store\Model\Resolver\Website $scopeResolver,
        Data\StockStatusBuilder $stockStatusBuilder,
        \Magento\CatalogInventory\Model\Resource\Stock\Status\CollectionFactory $itemsFactory,
        Data\LowStockResultBuilder $lowStockResultBuilder
    ) {
        $this->stockStatus = $stockStatus;
        $this->stockItemService = $stockItemService;
        $this->productRepository = $productRepository;
        $this->scopeResolver = $scopeResolver;
        $this->stockStatusBuilder = $stockStatusBuilder;
        $this->itemsFactory = $itemsFactory;
        $this->lowStockResultBuilder = $lowStockResultBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getProductStockStatus($productId, $websiteId, $stockId = Stock::DEFAULT_STOCK_ID)
    {
        $stockStatusData = $this->stockStatus->getProductStockStatus([$productId], $websiteId, $stockId);
        $stockStatus = empty($stockStatusData[$productId]) ? null : $stockStatusData[$productId];

        return $stockStatus;
    }

    /**
     * Assign Stock Status to Product
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $stockId
     * @param int $stockStatus
     * @return \Magento\CatalogInventory\Service\V1\StockStatusService
     */
    public function assignProduct(
        \Magento\Catalog\Model\Product $product,
        $stockId = Stock::DEFAULT_STOCK_ID,
        $stockStatus = null
    ) {
        $this->stockStatus->assignProduct($product, $stockId, $stockStatus);
        return $this;
    }

    /**
     * {inheritdoc}
     */
    public function getProductStockStatusBySku($sku)
    {
        $product = $this->productRepository->get($sku);
        $productId = $product->getId();
        if (!$productId) {
            throw new NoSuchEntityException("Product with SKU \"{$sku}\" does not exist");
        }

        $data = $this->stockStatus->getProductStockStatus(
            [$productId],
            $this->scopeResolver->getScope()->getId()
        );
        $stockStatus = (bool)$data[$productId];

        $result = [
            Data\StockStatus::STOCK_STATUS => $stockStatus,
            Data\StockStatus::STOCK_QTY => $this->stockItemService->getStockQty($productId)
        ];

        $this->stockStatusBuilder->populateWithArray($result);

        return $this->stockStatusBuilder->create();
    }

    /**
     * Retrieves a list of SKU's with low inventory qty
     *
     * {@inheritdoc}
     */
    public function getLowStockItems($lowStockCriteria)
    {
        /** @var \Magento\CatalogInventory\Model\Resource\Stock\Status\Collection $itemCollection */
        $itemCollection = $this->itemsFactory->create();
        $itemCollection->addWebsiteFilter($this->scopeResolver->getScope());
        $itemCollection->addQtyFilter($lowStockCriteria->getQty());
        $itemCollection->setCurPage($lowStockCriteria->getCurrentPage());
        $itemCollection->setPageSize($lowStockCriteria->getPageSize());

        $countOfItems = $itemCollection->getSize();
        $listOfSku = [];
        foreach ($itemCollection as $item) {
            $listOfSku[] = $item->getSku();
        }

        $this->lowStockResultBuilder->setSearchCriteria($lowStockCriteria);
        $this->lowStockResultBuilder->setTotalCount($countOfItems);
        $this->lowStockResultBuilder->setItems($listOfSku);
        return $this->lowStockResultBuilder->create();
    }
}
