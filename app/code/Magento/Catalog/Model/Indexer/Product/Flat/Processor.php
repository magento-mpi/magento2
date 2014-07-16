<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Flat;

class Processor extends \Magento\Indexer\Model\Indexer\AbstractProcessor
{
    /**
     * Indexer ID
     */
    const INDEXER_ID = 'catalog_product_flat';

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    protected $_state;

    /**
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $state
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Catalog\Model\Indexer\Product\Flat\State $state
    ) {
        $this->_indexer = $indexerFactory->create();
        $this->_state = $state;
    }

    /**
     * Reindex single row by id
     *
     * @param int $id
     * @return void
     */
    public function reindexRow($id)
    {
        if (!$this->_state->isFlatEnabled() || $this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexRow($id);
    }

    /**
     * Reindex multiple rows by ids
     *
     * @param int[] $ids
     * @return void
     */
    public function reindexList($ids)
    {
        if (!$this->_state->isFlatEnabled() || $this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run full reindex
     *
     * @return void
     */
    public function reindexAll()
    {
        if (!$this->_state->isFlatEnabled()) {
            return;
        }
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product flat indexer as invalid
     *
     * @return void
     */
    public function markIndexerAsInvalid()
    {
        if (!$this->_state->isFlatEnabled()) {
            return;
        }
        $this->getIndexer()->invalidate();
    }
}
