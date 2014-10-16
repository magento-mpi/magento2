<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Indexer
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model\Indexer;

abstract class AbstractProcessor
{
    /**
     * Indexer ID
     */
    const INDEXER_ID = '';

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $_indexer;

    /**
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory
    ) {
        $this->_indexer = $indexerFactory->create();
    }

    /**
     * Get indexer
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    public function getIndexer()
    {
        if (!$this->_indexer->getId()) {
            $this->_indexer->load(static::INDEXER_ID);
        }
        return $this->_indexer;
    }

    /**
     * Run Row reindex
     *
     * @param int $id
     * @return void
     */
    public function reindexRow($id)
    {
        if ($this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexRow($id);
    }

    /**
     * Run List reindex
     *
     * @param int[] $ids
     * @return void
     */
    public function reindexList($ids)
    {
        if ($this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run Full reindex
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product price indexer as invalid
     *
     * @return void
     */
    public function markIndexerAsInvalid()
    {
        $this->getIndexer()->invalidate();
    }

    /**
     * Get processor indexer ID
     *
     * @return string
     */
    public function getIndexerId()
    {
        return static::INDEXER_ID;
    }
}
