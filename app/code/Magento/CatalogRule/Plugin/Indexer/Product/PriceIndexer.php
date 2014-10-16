<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogRule\Plugin\Indexer\Product;

class PriceIndexer
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Price\Processor
     */
    protected $priceProcessor;

    /**
     * @param \Magento\Catalog\Model\Indexer\Product\Price\Processor $priceProcessor
     */
    public function __construct(\Magento\Catalog\Model\Indexer\Product\Price\Processor $priceProcessor)
    {
        $this->priceProcessor = $priceProcessor;
    }

    /**
     * Invalidate price indexer
     *
     * @param \Magento\CatalogRule\Model\Indexer\IndexBuilder $subject
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterReindexFull(\Magento\CatalogRule\Model\Indexer\IndexBuilder $subject)
    {
        $this->priceProcessor->markIndexerAsInvalid();
    }

    /**
     * @param \Magento\CatalogRule\Model\Indexer\IndexBuilder $subject
     * @param callable $proceed
     * @param array $productIds
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundReindexByIds(
        \Magento\CatalogRule\Model\Indexer\IndexBuilder $subject,
        \Closure $proceed,
        array $productIds
    ) {
        $proceed($productIds);
        $this->priceProcessor->reindexList($productIds);
    }

    /**
     * @param \Magento\CatalogRule\Model\Indexer\IndexBuilder $subject
     * @param callable $proceed
     * @param int $productId
     * @return void
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundReindexById(
        \Magento\CatalogRule\Model\Indexer\IndexBuilder $subject,
        \Closure $proceed,
        $productId
    ) {
        $proceed($productId);
        $this->priceProcessor->reindexRow($productId);
    }
}
