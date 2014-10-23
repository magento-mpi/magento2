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
class PriceScope extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $indexer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerInterface $indexer
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerInterface $indexer,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
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
    public function afterSave()
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
