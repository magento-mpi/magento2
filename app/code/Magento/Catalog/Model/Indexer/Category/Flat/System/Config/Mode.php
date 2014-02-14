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
class Mode extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $flatIndexer;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerInterface $flatIndexer
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Indexer\Model\IndexerInterface $flatIndexer,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->flatIndexer = $flatIndexer;
        parent::__construct($context, $registry, $storeManager, $config, $resource, $resourceCollection, $data);
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
        if ($this->isValueChanged()) {
            $this->flatIndexer->load(\Magento\Catalog\Model\Indexer\Category\Flat\State::INDEXER_ID);
            if ($this->getValue()) {
                $this->flatIndexer->invalidate();
            } else {
                $this->flatIndexer->setScheduled(false);
            }
        }
    }
}
