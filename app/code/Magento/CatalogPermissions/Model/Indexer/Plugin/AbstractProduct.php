<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

abstract class AbstractProduct
{
    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param \Magento\CatalogPermissions\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        \Magento\CatalogPermissions\App\ConfigInterface $config
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->config = $config;
    }

    /**
     * Reindex by product if indexer is enabled and not scheduled
     *
     * @param int[] $productIds
     * @return void
     */
    protected function reindex(array $productIds)
    {
        if ($this->config->isEnabled()) {
            $indexer = $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID);
            if (!$indexer->isScheduled()) {
                $indexer->reindexList($productIds);
            }
        }
    }
}
