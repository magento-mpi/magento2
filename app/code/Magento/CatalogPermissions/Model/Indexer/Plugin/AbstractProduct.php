<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

abstract class AbstractProduct
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\CatalogPermissions\App\ConfigInterface
     */
    protected $config;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\CatalogPermissions\App\ConfigInterface $config
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\CatalogPermissions\App\ConfigInterface $config
    ) {
        $this->indexer = $indexer;
        $this->config = $config;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Reindex by product if indexer is enabled and not scheduled
     *
     * @param int[] $productIds
     * @return void
     */
    protected function reindex(array $productIds)
    {
        if ($this->config->isEnabled() && !$this->getIndexer()->isScheduled()) {
            $this->getIndexer()->reindexList($productIds);
        }
    }
}
