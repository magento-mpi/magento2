<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class AbstractStore
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $state;

    /**
     * Category flat resource
     *
     * @var \Magento\Catalog\Model\Resource\Category\Flat
     */
    protected $flatResource;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $config
     * @param \Magento\Catalog\Model\Resource\Category\Flat $flatResource
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $config,
        \Magento\Catalog\Model\Resource\Category\Flat $flatResource
    ) {
        $this->indexer = $indexer;
        $this->state = $config;
        $this->flatResource = $flatResource;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     */
    protected function invalidateIndexer()
    {
        if ($this->state->isFlatEnabled()) {
            $this->getIndexer()->invalidate();
        }
    }

    /**
     * Cleaning a data after removing store
     *
     * @param $storeIds
     */
    protected function cleanStoreData($storeIds)
    {
        $this->flatResource->deleteStores($storeIds);
    }
}
