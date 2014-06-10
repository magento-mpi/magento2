<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogInventory
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\CatalogInventory\Model\Indexer\Stock;

/**
 * Abstract action reindex class
 *
 * @package Magento\CatalogInventory\Model\Indexer\Stock
 */
abstract class AbstractAction
{
    /**
     * Resource instance
     *
     * @var \Magento\Framework\App\Resource
     */
    protected $_resource;

    /**
     * Logger instance
     *
     * @var  \Magento\Framework\Logger
     */
    protected $_logger;

    /**
     * @var \Magento\CatalogInventory\Model\Resource\Indexer\StockFactory
     */
    protected $_indexerFactory;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_catalogProductType;


    /**
     * @var \Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Stock Indexer models per product type
     * Sorted by priority
     *
     * @var array
     */
    protected $_indexers = array();

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\CatalogInventory\Model\Resource\Indexer\StockFactory $indexerFactory
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\Logger $logger,
        \Magento\CatalogInventory\Model\Resource\Indexer\StockFactory $indexerFactory,
        \Magento\Catalog\Model\Product\Type $catalogProductType
    ) {
        $this->_resource = $resource;
        $this->_logger = $logger;
        $this->_indexerFactory = $indexerFactory;
        $this->_catalogProductType = $catalogProductType;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     */
    abstract public function execute($ids);

    /**
     * Retrieve connection instance
     *
     * @return bool|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection('write');
        }
        return $this->_connection;
    }

    /**
     * Retrieve Stock Indexer Models per Product Type
     *
     * @return array
     */
    protected function _getTypeIndexers()
    {
        if (empty($this->_indexers)) {
            foreach ($this->_catalogProductType->getTypesByPriority() as $typeId => $typeInfo) {
                $indexerClassName = isset($typeInfo['stock_indexer']) ? $typeInfo['stock_indexer'] : '';

                $indexer = $this->_indexerFactory->create(
                    $indexerClassName
                )->setTypeId(
                        $typeId
                    )->setIsComposite(
                        !empty($typeInfo['composite'])
                    );

                $this->_indexers[$typeId] = $indexer;
            }
        }
        return $this->_indexers;
    }

    /**
     * Returns table name for given entity
     *
     * @param string $entityName
     * @return string
     */
    protected function _getTable($entityName)
    {
        return $this->_resource->getTableName($entityName);
    }

    /**
     * Retrieve product relations by children
     *
     * @param int|array $childIds
     * @return array
     */
    public function getRelationsByChild($childIds)
    {
        $adapter = $this->_getConnection();
        $select = $adapter->select()->from(
            $this->_getTable('catalog_product_relation'),
            'parent_id'
        )->where(
                'child_id IN(?)',
                $childIds
            );

        return $adapter->fetchCol($select);
    }

    /**
     * Refresh entities index
     *
     * @param array $productIds
     * @return array Affected ids
     */
    protected function _reindexRows($productIds = array())
    {
        $adapter = $this->_getConnection();
        if (!is_array($productIds)) {
            $productIds = array($productIds);
        }
        $parentIds = $this->getRelationsByChild($productIds);
        if ($parentIds) {
            $processIds = array_merge($parentIds, $productIds);
        } else {
            $processIds = $productIds;
        }

        // retrieve product types by processIds
        $select = $adapter->select()->from(
            $this->_getTable('catalog_product_entity'),
            array('entity_id', 'type_id')
        )->where(
                'entity_id IN(?)',
                $processIds
            );
        $pairs = $adapter->fetchPairs($select);

        $byType = array();
        foreach ($pairs as $productId => $typeId) {
            $byType[$typeId][$productId] = $productId;
        }

        $indexers = $this->_getTypeIndexers();
        foreach ($indexers as $indexer) {
            if (isset($byType[$indexer->getTypeId()])) {
                $indexer->reindexEntity($byType[$indexer->getTypeId()]);
            }
        }

        return $this;
    }
}
