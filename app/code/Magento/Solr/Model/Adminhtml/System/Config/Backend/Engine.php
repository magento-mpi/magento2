<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Solr\Model\Adminhtml\System\Config\Backend;

/**
 * Catalog search backend model
 *
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Engine extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\Indexer\Model\IndexerFactory
     */
    protected $indexerFactory;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->indexerFactory = $indexerFactory;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After save call
     * Invalidate catalog search index if engine was changed
     *
     * @return $this
     */
    protected function _afterSave()
    {
        parent::_afterSave();

        if ($this->isValueChanged()) {
            $indexer = $this->indexerFactory->create();
            $indexer->load(\Magento\CatalogSearch\Model\Indexer\Fulltext::INDEXER_ID);
            $indexer->invalidate();
        }

        return $this;
    }
}
