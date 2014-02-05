<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

class AbstractStore
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $state;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $state
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $state
    ) {
        $this->indexer = $indexer;
        $this->state = $state;
    }

    /**
     * Return own indexer object
     *
     * @return \Magento\Indexer\Model\IndexerInterface
     */
    protected function getIndexer()
    {
        if (!$this->indexer->getId()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
        }
        return $this->indexer;
    }

    /**
     * Invalidate indexer
     */
    protected function invalidateIndexer()
    {
        if ($this->state->isFlatEnabled()) {
            $this->getIndexer()->invalidate();
        }
    }
}
