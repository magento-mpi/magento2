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
     * @var \Magento\Mview\ProcessorInterface
     */
    protected $mviewProcessor;

    /**
     * @param Config $config
     * @param IndexerFactory $indexerFactory
     * @param Indexer\CollectionFactory $indexersFactory
     * @param \Magento\Mview\ProcessorInterface $mviewProcessor
     */
    public function __construct(
        Config $config,
        IndexerFactory $indexerFactory,
        Indexer\CollectionFactory $indexersFactory,
        \Magento\Mview\ProcessorInterface $mviewProcessor
    ) {
        $this->config = $config;
        $this->indexerFactory = $indexerFactory;
        $this->indexersFactory = $indexersFactory;
        $this->mviewProcessor = $mviewProcessor;
    }

    /**
     * Regenerate indexes for all invalid indexers
     */
    public function reindexAllInvalid()
    {
        foreach ($this->config->getIndexerIds() as $indexerId) {
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
        foreach ($indexers as $indexer) {
            $indexer->reindexAll();
        }
    }

    /**
     * Update indexer views
     */
    public function updateMview()
    {
        $this->mviewProcessor->update('indexer');
    }

    /**
     * Clean indexer view changelogs
     */
    public function clearChangelog()
    {
        $this->mviewProcessor->clearChangelog('indexer');
    }
}
