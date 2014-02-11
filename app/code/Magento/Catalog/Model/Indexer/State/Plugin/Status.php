<?php
/**
 * Plugin for \Magento\Indexer\Model\Indexer\State model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\State\Plugin;

class Status
{
    /**
     * @var \Magento\Indexer\Model\Indexer\StateFactory
     */
    protected $stateFactory;

    /**
     * ids list
     *
     * @var array
     */
    protected $indexerIds = array(
        \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID,
        \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID
    );

    /**
     * @param \Magento\Indexer\Model\Indexer\StateFactory $stateFactory
     */
    public function __construct(\Magento\Indexer\Model\Indexer\StateFactory $stateFactory)
    {
        $this->stateFactory = $stateFactory;
    }

    /**
     * Synchronize status for indexers
     *
     * @param \Magento\Indexer\Model\Indexer\State $state
     * @return \Magento\Indexer\Model\Indexer\State
     */
    public function afterSetStatus(\Magento\Indexer\Model\Indexer\State $state)
    {
        if (in_array($state->getIndexerId(), $this->indexerIds)) {
            $indexerId = $state->getIndexerId() == \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID
                ? \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID
                : \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID;

            $relatedIndexerState = $this->stateFactory->create()
                ->load($indexerId, 'indexer_id');

            $relatedIndexerState->setData('status', $state->getStatus())
                ->save();
        }

        return $state;
    }
}
