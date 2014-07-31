<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Indexer\Plugin\Product;

use Magento\Indexer\Model\IndexerInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

class Action
{
    /**
     * @var IndexerInterface
     */
    protected $indexer;

    /**
     * @param IndexerInterface $indexer
     */
    public function __construct(
        IndexerInterface $indexer
    ) {
        $this->indexer = $indexer;
    }

    /**
     * Return indexer object
     *
     * @return IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(Fulltext::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Reindex on product attribute mass change
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param callable $closure
     * @param array $productIds
     * @param array $attrData
     * @param int $storeId
     * @return \Magento\Catalog\Model\Product\Action
     */
    public function aroundUpdateAttributes(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $closure,
        array $productIds,
        array $attrData,
        $storeId
    ) {
        $closure($productIds, $attrData, $storeId);
        if (!$this->getIndexer()->isScheduled()) {
            $this->getIndexer()->reindexList(array_unique($productIds));
        }
        return $subject;
    }

    /**
     * Reindex on product websites mass change
     *
     * @param \Magento\Catalog\Model\Product\Action $subject
     * @param callable $closure
     * @param array $productIds
     * @param array $websiteIds
     * @param string $type
     * @return \Magento\Catalog\Model\Product\Action
     */
    public function aroundUpdateWebsites(
        \Magento\Catalog\Model\Product\Action $subject,
        \Closure $closure,
        array $productIds,
        array $websiteIds,
        $type
    ) {
        $closure($productIds, $websiteIds, $type);
        if (!$this->getIndexer()->isScheduled()) {
            $this->getIndexer()->reindexList(array_unique($productIds));
        }
        return $subject;
    }
}
