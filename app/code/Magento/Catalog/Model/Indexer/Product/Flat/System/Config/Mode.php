<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Flat\System\Config;

/**
 * Flat product on/off backend
 */
class Mode extends \Magento\Core\Model\Config\Value
{
    /**
     * @var \Magento\Catalog\Model\Indexer\Product\Flat\Processor
     */
    protected $_productFlatIndexerProcessor;

    /**
     * @param \Magento\Model\Context $context
     * @param \Magento\Registry $registry
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Model\Context $context,
        \Magento\Registry $registry,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\ConfigInterface $config,
        \Magento\Catalog\Model\Indexer\Product\Flat\Processor $productFlatIndexerProcessor,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_productFlatIndexerProcessor = $productFlatIndexerProcessor;
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
     */
    public function processValue()
    {
        if ($this->isValueChanged()) {
            if ($this->getValue()) {
                $this->_productFlatIndexerProcessor->markIndexerAsInvalid();
            } else {
                $this->_productFlatIndexerProcessor->getIndexer()->setScheduled(false);
            }
        }
    }
}
