<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\System\Config;

/**
 * Flat category on/off backend
 */
class Mode extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $flatIndexer;

    /**
     * @var \Magento\Indexer\Model\Indexer\State
     */
    protected $indexerState;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerInterface $flatIndexer
     * @param \Magento\Indexer\Model\Indexer\State $indexerState
     * @param \Magento\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerInterface $flatIndexer,
        \Magento\Indexer\Model\Indexer\State $indexerState,
        \Magento\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->flatIndexer = $flatIndexer;
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
     * Process flat enabled mode change
     *
     * @return void
     */
    public function processValue()
    {
        if ((bool)$this->getValue() != (bool)$this->getOldValue()) {
            if ((bool)$this->getValue()) {
                $this->indexerState->loadByIndexer(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
                $this->indexerState->setStatus(\Magento\Indexer\Model\Indexer\State::STATUS_INVALID);
                $this->indexerState->save();
            } else {
                $this->flatIndexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
                $this->flatIndexer->setScheduled(false);
            }
        }
    }
}
