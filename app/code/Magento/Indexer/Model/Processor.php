<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Indexer\Model;

class Processor
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @var Indexer\CollectionFactory
     */
    protected $indexersFactory;

    /**
     * @param Config $config
     * @param IndexerFactory $indexerFactory
     * @param Indexer\CollectionFactory $indexersFactory
     */
    public function __construct(
        Config $config,
        IndexerFactory $indexerFactory,
        Indexer\CollectionFactory $indexersFactory
    ) {
        $this->config = $config;
        $this->indexerFactory = $indexerFactory;
        $this->indexersFactory = $indexersFactory;
    }

    /**
     * Regenerate indexes for all invalid indexers
     */
    public function reindexAllInvalid()
    {
        foreach (array_keys($this->config->getAll()) as $indexerId) {
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            if ($indexer->getState()->getStatus() == Indexer\State::STATUS_INVALID) {
                $indexer->reindexAll();
            }
        }
    }

    /**
     * Regenerate indexes for all indexers
     */
    public function reindexAll()
    {
        /** @var Indexer[] $indexers */
        $indexers = $this->indexersFactory->create()->getItems();
        $isGroupBuilt = array();
        foreach ($indexers as $indexer) {
            if (!$indexer->getGroup() || !isset($isGroupBuilt[$indexer->getGroup()])
                || !$isGroupBuilt[$indexer->getGroup()]
            ) {
                $indexer->reindexAll();
                if ($indexer->getGroup()) {
                    $isGroupBuilt[$indexer->getGroup()] = true;
                }
            }
        }
    }
}
