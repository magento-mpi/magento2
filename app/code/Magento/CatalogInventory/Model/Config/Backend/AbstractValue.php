<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Config Backend Model
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

abstract class AbstractValue extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_stockStatus;

    /**
     * @var \Magento\CatalogInventory\Model\Indexer\Stock\Processor
     */
    protected $_stockIndexerProcessor;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\CatalogInventory\Model\Indexer\Stock\Processor $stockIndexerProcessor,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_stockIndexerProcessor = $stockIndexerProcessor;
        $this->_stockStatus = $stockStatus;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }
}
