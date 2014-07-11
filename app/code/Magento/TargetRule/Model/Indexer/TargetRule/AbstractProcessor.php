<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_TargetRule
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\TargetRule\Model\Indexer\TargetRule;

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
     * State container
     *
     * @var Status\Container
     */
    protected $_statusContainer;

    /**
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\TargetRule\Model\Indexer\TargetRule\Status\Container $statusContainer
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\TargetRule\Model\Indexer\TargetRule\Status\Container $statusContainer
    ) {
        $this->_indexer = $indexerFactory->create();
        $this->_statusContainer = $statusContainer;
    }

    /**
     * Get indexer
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    public function getIndexer()
    {
        if (!$this->_indexer->getId()) {
            $this->_indexer->load($this->getIndexerId());
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
     * Get processor state container
     *
     * @return Status\Container
     */
    public function getStatusContainer()
    {
        return $this->_statusContainer;
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

    /**
     * Is full reindex passed
     *
     * @return bool
     */
    public function isFullReindexPassed()
    {
        return $this->getStatusContainer()->isFullReindexPassed($this->getIndexerId());
    }

    /**
     * Set full reindex passed
     *
     * @return void
     */
    public function setFullReindexPassed()
    {
        $this->getStatusContainer()->setFullReindexPassed($this->getIndexerId());
    }
}
