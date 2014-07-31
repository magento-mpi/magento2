<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogSearch\Model\Indexer\Plugin;

use Magento\Indexer\Model\IndexerInterface;
use Magento\CatalogSearch\Model\Indexer\Fulltext;

class Product
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
     * Reindex by product if indexer is not scheduled
     *
     * @param int $productId
     * @return void
     */
    protected function reindex($productId)
    {
        if (!$this->getIndexer()->isScheduled()) {
            $this->getIndexer()->reindexRow($productId);
        }
    }

    /**
     * Reindex on product save
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterSave(\Magento\Catalog\Model\Product $product)
    {
        $this->reindex($product->getId());
        return $product;
    }

    /**
     * Reindex on product delete
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\Catalog\Model\Product
     */
    public function afterDelete(\Magento\Catalog\Model\Product $product)
    {
        $this->reindex($product->getId());
        return $product;
    }
}
