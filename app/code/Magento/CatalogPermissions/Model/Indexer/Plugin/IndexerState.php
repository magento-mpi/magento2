<?php
/**
 * Plugin for \Magento\Indexer\Model\Indexer\State model
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\Plugin;

class IndexerState
{
    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $state;

    /**
     * Related indexers IDs
     *
     * @var int[]
     */
    protected $indexerIds = array(
        \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID,
        \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID,
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
     * @param \Magento\Indexer\Model\Indexer\State $state
     * @return \Magento\Indexer\Model\Indexer\State
     */
    public function afterSetStatus(\Magento\Indexer\Model\Indexer\State $state)
    {
        if (in_array($state->getIndexerId(), $this->indexerIds)) {
            $indexerId = $state->getIndexerId() == \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID
                ? \Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID
                : \Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID;

            $relatedState = $this->state->loadByIndexer($indexerId);
            $relatedState->setData('status', $state->getStatus());
            $relatedState->save();
        }

        return $state;
    }
}
