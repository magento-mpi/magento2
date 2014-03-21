<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price\System\Config;

/**
 * Price scope backend model
 */
class PriceScope extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->indexer = $indexer;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * Set after commit callback
     *
     * @return \Magento\Catalog\Model\Indexer\Product\Price\System\Config\PriceScope
     */
    protected function _afterSave()
    {
        $this->_getResource()->addCommitCallback(array($this, 'processValue'));
        return $this;
    }

    /**
     * Process product price scope change
     *
     * @return void
     */
    public function processValue()
    {
        if ($this->isValueChanged()) {
            $this->indexer->load(\Magento\Catalog\Model\Indexer\Product\Price\Processor::INDEXER_ID)->invalidate();
        }
    }
}
