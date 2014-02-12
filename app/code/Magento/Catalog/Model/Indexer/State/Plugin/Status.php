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
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $state;

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
     * @param \Magento\Indexer\Model\Indexer\State $state
     */
    public function __construct(\Magento\Indexer\Model\Indexer\State $state)
    {
        $this->state = $state;
    }

    /**
     * Synchronize status for indexers
     *
     * @param \Magento\Object $state
     * @return \Magento\Object
     */
    public function afterSetStatus(\Magento\Object $state)
    {
        if (in_array($state->getIndexerId(), $this->indexerIds)) {
            $indexerId = $state->getIndexerId() == \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID
                ? \Magento\Catalog\Model\Indexer\Product\Category::INDEXER_ID
                : \Magento\Catalog\Model\Indexer\Category\Product::INDEXER_ID;

            $relatedIndexerState = $this->state->load($indexerId, 'indexer_id');

            $relatedIndexerState->setData('status', $state->getStatus())
                ->save();
        }

        return $state;
    }
}
