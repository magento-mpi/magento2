<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Product\Eav;

class Processor
{
    /**
     * Indexer ID
     */
    const INDEXER_ID = 'catalog_product_attribute';

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
     * Get indexer instance
     *
     * @return \Magento\Indexer\Model\Indexer
     */
    public function getIndexer()
    {
        if (!$this->_indexer->getId()) {
            $this->_indexer->load(static::INDEXER_ID);
        }
        return $this->_indexer;
    }

    /**
     * Reindex single row by id
     *
     * @param int $id
     * @return void
     */
    public function reindexRow($id)
    {
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
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run full reindex
     *
     * @return void
     */
    public function reindexAll()
    {
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product attribute indexer as invalid
     *
     * @return void
     */
    public function markIndexerAsInvalid()
    {
        $this->getIndexer()->invalidate();
    }
}
