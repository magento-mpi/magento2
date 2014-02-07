<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Plugin;

abstract class AbstractStore extends \Magento\Catalog\Model\Indexer\AbstractStore
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\State
     */
    protected $state;

    /**
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param string $indexerCode
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\State $state
     */
    public function __construct(
        \Magento\Indexer\Model\IndexerInterface $indexer,
        $indexerCode,
        \Magento\Catalog\Model\Indexer\Category\Flat\State $state
    ) {
        $this->state = $state;
        parent::__construct($indexer, $indexerCode);
    }

    /**
     * {@inheritdoc}
     */
    protected function invalidateIndexer()
    {
        if ($this->state->isFlatEnabled()) {
            $this->getIndexer()->invalidate();
        }
    }
}
