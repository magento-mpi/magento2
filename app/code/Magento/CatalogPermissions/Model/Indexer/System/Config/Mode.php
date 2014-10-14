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
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $indexerState;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Indexer\Model\Indexer\State $indexerState
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Indexer\Model\Indexer\State $indexerState,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->indexer = $indexer;
        $this->indexerState = $indexerState;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Set after commit callback
     *
     * @return $this
     */
    public function afterSave()
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
                // Turn scheduled mode off for Category indexer
                $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Category::INDEXER_ID);
                $this->indexer->setScheduled(false);
                // Turn scheduled mode off for Product indexer
                $this->indexer->load(\Magento\CatalogPermissions\Model\Indexer\Product::INDEXER_ID);
                $this->indexer->setScheduled(false);
            }
        }
    }
}
