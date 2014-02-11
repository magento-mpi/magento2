<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price;

class Processor
{
    /**
     * Indexer ID
     */
    const INDEXER_ID = 'catalog_product_price';

    /**
     * @var \Magento\Indexer\Model\Indexer
     */
    protected $_indexer;

    /**
     * @param \Magento\Indexer\Model\Indexer $indexer
     */
    public function __construct(
       \Magento\Indexer\Model\Indexer $indexer
    ) {
        $this->_indexer = $indexer;
    }

    /**
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
     * @param int $id
     */
    public function reindexRow($id)
    {
        if ($this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexRow($id);
    }

    /**
     * @param int[] $ids
     */
    public function reindexList($ids)
    {
        if ($this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run full reindex
     */
    public function reindexAll()
    {
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product price indexer as invalid
     */
    public function markIndexerAsInvalid()
    {
        $this->getIndexer()->invalidate();
    }
}
