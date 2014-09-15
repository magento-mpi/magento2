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

abstract class AbstractProcessor extends \Magento\Indexer\Model\Indexer\AbstractProcessor
{
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
     * Get processor state container
     *
     * @return Status\Container
     */
    public function getStatusContainer()
    {
        return $this->_statusContainer;
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
        $this->getIndexer()->getState()->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_VALID)->save();
        $this->getStatusContainer()->setFullReindexPassed($this->getIndexerId());
    }
}
