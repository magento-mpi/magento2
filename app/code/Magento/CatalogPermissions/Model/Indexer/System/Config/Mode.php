<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogPermissions\Model\Indexer\System\Config;

/**
 * Catalog Permissions on/off backend
 */
class Mode extends \Magento\Framework\App\Config\Value
{
    /** @var \Magento\Indexer\Model\IndexerRegistry */
    protected $indexerRegistry;

    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $indexerState;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerRegistry $indexerRegistry
     * @param \Magento\Indexer\Model\Indexer\State $indexerState
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerRegistry $indexerRegistry,
        \Magento\Indexer\Model\Indexer\State $indexerState,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->indexerRegistry = $indexerRegistry;
        $this->indexerState = $indexerState;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Set after commit callback
     *
     * @return $this
     */
    protected function _afterSave()
    {
        $this->_getResource()->addCommitCallback(array($this, 'processValue'));
        return $this;
    }

    /**
     * Process permissions enabled mode change
     *
     * @return void
     */
    public function processValue()
    {
        if ((bool)$this->getValue() != (bool)$this->getOldValue()) {
            if ((bool)$this->getValue()) {
                $this->indexerState->loadByIndexer(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
                $this->indexerState->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_INVALID);
                $this->indexerState->save();
            } else {
                $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID)
                    ->setScheduled(false);
                $this->indexerRegistry->get(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID)
                    ->setScheduled(false);
            }
        }
    }
}
