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
     * @var \Magento\Catalog\Helper\Product\Flat
     */
    protected $_helper;

    /**
     * @param \Magento\Indexer\Model\Indexer $indexer
     * @param \Magento\Catalog\Helper\Product\Flat $helper
     */
    public function __construct(
       \Magento\Indexer\Model\Indexer $indexer,
       \Magento\Catalog\Helper\Product\Flat $helper
    ) {
        $this->_indexer = $indexer;
        $this->_helper = $helper;
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
        if (!$this->_helper->isEnabled() || $this->getIndexer()->isScheduled()) {
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
        if (!$this->_helper->isEnabled() || $this->getIndexer()->isScheduled()) {
            return;
        }
        $this->getIndexer()->reindexList($ids);
    }

    /**
     * Run full reindex
     */
    public function reindexAll()
    {
        if (!$this->_helper->isEnabled()) {
            return;
        }
        $this->getIndexer()->reindexAll();
    }

    /**
     * Mark Product flat indexer as invalid
     */
    public function markIndexerAsInvalid()
    {
        $this->getIndexer()->invalidate();
    }
}
