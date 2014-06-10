<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Inventory Backorders Config Backend Model
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogInventory\Model\Config\Backend;

class Backorders extends \Magento\Framework\App\Config\Value
{
    /**
     * @var \Magento\CatalogInventory\Model\Stock\Status
     */
    protected $_stockStatus;

    /**
     * @var \Magento\Indexer\Model\IndexerInterface
     */
    protected $_stockIndexer;

    /**
     * @param \Magento\Framework\Model\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $config
     * @param \Magento\CatalogInventory\Model\Stock\Status $stockStatus
     * @param \Magento\Indexer\Model\IndexerInterface $stockIndexer
     * @param \Magento\Framework\Model\Resource\AbstractResource $resource
     * @param \Magento\Framework\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\App\Config\ScopeConfigInterface $config,
        \Magento\CatalogInventory\Model\Stock\Status $stockStatus,
        \Magento\Indexer\Model\IndexerInterface $stockIndexer,
        \Magento\Framework\Model\Resource\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_stockIndexer = $stockIndexer;
        $this->_stockIndexer->load(\Magento\CatalogInventory\Model\Indexer\Stock\Processor::INDEXER_ID);
        $this->_stockStatus = $stockStatus;
        parent::__construct($context, $registry, $config, $resource, $resourceCollection, $data);
    }

    /**
     * After change Catalog Inventory Manage value process
     *
     * @return $this
     */
    protected function _afterSave()
    {
        if ($this->isValueChanged() && (
                $this->getOldValue() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
                || $this->getValue() == \Magento\CatalogInventory\Model\Stock::BACKORDERS_NO
            )
        ) {
            $this->_stockIndexer->invalidate();
            $this->_stockStatus->rebuild();
        }
        return $this;
    }
}
