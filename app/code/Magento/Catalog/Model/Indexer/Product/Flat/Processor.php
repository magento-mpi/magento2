<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat;

class Processor
{
    /**
     * Indexer ID
     */
    const INDEXER_ID = 'catalog_product_flat';

    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $_indexer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\State
     */
    protected $_state;

    /**
     * @param \Magento\Indexer\Model\Indexer $indexer
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\State $state
     */
    public function __construct(
       \Magento\Indexer\Model\Indexer $indexer,
       \Magento\Catalog\Model\Indexer\Product\Flat\State $state
    ) {
        $this->_indexer = $indexer;
        $this->_state = $state;
    }

    /**
     * Get indexer instance
     *
     * @return \Magento\Indexer\Model\Indexer
     */
    public function getIndexer()
    {
        if (!$this->_indexer->getId()) {
            $this->_indexer->load(self::INDEXER_ID);
        }
        return $this->_indexer;
    }

    /**
     * Reindex single row by id
     *
     * @param int $id
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
     */
    public function markIndexerAsInvalid()
    {
        if (!$this->_state->isFlatEnabled()) {
            return;
        }
        $this->getIndexer()->invalidate();
    }
}
