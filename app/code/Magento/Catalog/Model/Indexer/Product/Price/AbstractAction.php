<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Product\Price;

/**
 * Abstract action reindex class
 *
 * @package Magento\Catalog\Model\Indexer\Product\Price
 */
abstract class AbstractAction
{
    /**
     * Default Product Type Price indexer resource model
     *
     * @var string
     */
    protected $_defaultPriceIndexer = 'Magento\Catalog\Model\Resource\Product\Indexer\Price\DefaultPrice';

    /**
     * Logger instance
     *
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Resource instance
     *
     * @var \Magento\App\Resource
     */
    protected $_resource;

    /**
     * @var \Magento\DB\Adapter\AdapterInterface
     */
    protected $_connection;

    /**
     * Core config model
     *
     * @var \Magento\App\ConfigInterface
     */
    protected $_config;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * Currency factory
     *
     * @var \Magento\Directory\Model\CurrencyFactory
     */
    protected $_currencyFactory;

    /**
     * Locale
     *
     * @var \Magento\Core\Model\LocaleInterface
     */
    protected $_locale;

    /**
     * @var \Magento\Stdlib\DateTime
     */
    protected $_dateTime;

    /**
     * @var \Magento\Catalog\Model\Product\Type
     */
    protected $_catalogProductType;

    /**
     * Indexer price factory
     *
     * @var \Magento\Catalog\Model\Resource\Product\Indexer\Price\Factory
     */
    protected $_indexerPriceFactory;

    /**
     * @var array|null
     */
    protected $_indexers;

    /**
     * Flag that defines if need to use "_idx" index table suffix instead of "_tmp"
     *
     * @var bool
     */
    protected $_useIdxTable = false;

    /**
     * @param \Magento\Logger $logger
     * @param \Magento\App\Resource $resource
     * @param \Magento\App\ConfigInterface $config
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Directory\Model\CurrencyFactory $currencyFactory
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Stdlib\DateTime $dateTime
     * @param \Magento\Catalog\Model\Product\Type $catalogProductType
     * @param \Magento\Catalog\Model\Resource\Product\Indexer\Price\Factory $indexerPriceFactory
     */
    public function __construct(
        \Magento\Logger $logger,
        \Magento\App\Resource $resource,
        \Magento\App\ConfigInterface $config,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Directory\Model\CurrencyFactory $currencyFactory,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Stdlib\DateTime $dateTime,
        \Magento\Catalog\Model\Product\Type $catalogProductType,
        \Magento\Catalog\Model\Resource\Product\Indexer\Price\Factory $indexerPriceFactory
    ) {
        $this->_logger = $logger;
        $this->_resource = $resource;
        $this->_config = $config;
        $this->_storeManager = $storeManager;
        $this->_currencyFactory = $currencyFactory;
        $this->_locale = $locale;
        $this->_dateTime = $dateTime;
        $this->_catalogProductType = $catalogProductType;
        $this->_indexerPriceFactory = $indexerPriceFactory;
    }

    /**
     * Retrieve connection instance
     *
     * @return bool|\Magento\DB\Adapter\AdapterInterface
     */
    protected function _getConnection()
    {
        if (null === $this->_connection) {
            $this->_connection = $this->_resource->getConnection('default');
        }
        return $this->_connection;
    }

    /**
     * Execute action for given ids
     *
     * @param array|int $ids
     */
    abstract public function execute($ids);

    /**
     * Synchronize data between index storage and original storage
     *
     * @param array $processIds
     * @return \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
     */
    protected function _syncData(array $processIds = array())
    {
        // delete invalid rows
        $select = $this->_getConnection()->select()
            ->from(array('index_price' => $this->_getTable('catalog_product_index_price')), null)
            ->joinLeft(
                array('ip_tmp' => $this->_getIdxTable()),
                'index_price.entity_id = ip_tmp.entity_id AND index_price.website_id = ip_tmp.website_id',
                array()
            )
            ->where('ip_tmp.entity_id IS NULL');
        if (!empty($processIds)) {
            $select->where('index_price.entity_id IN(?)', $processIds);
        }
        $sql = $select->deleteFromSelect('index_price');
        $this->_getConnection()->query($sql);

        $this->_insertFromTable($this->_getIdxTable(), $this->_getTable('catalog_product_index_price'));
        return $this;
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
     * Prepare website current dates table
     *
     * @return \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
     */
    protected function _prepareWebsiteDateTable()
    {
        $write = $this->_getConnection();
        $baseCurrency = $this->_config->getValue(\Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE, 'default');

        $select = $write->select()
            ->from(
                array('cw' => $this->_getTable('core_website')),
                array('website_id')
            )
            ->join(
                array('csg' => $this->_getTable('core_store_group')),
                'cw.default_group_id = csg.group_id',
                array('store_id' => 'default_store_id')
            )
            ->where('cw.website_id != 0');


        $data = array();
        foreach ($write->fetchAll($select) as $item) {
            /** @var $website \Magento\Core\Model\Website */
            $website = $this->_storeManager->getWebsite($item['website_id']);

            if ($website->getBaseCurrencyCode() != $baseCurrency) {
                $rate = $this->_currencyFactory->create()
                    ->load($baseCurrency)
                    ->getRate($website->getBaseCurrencyCode());
                if (!$rate) {
                    $rate = 1;
                }
            } else {
                $rate = 1;
            }

            /** @var $store \Magento\Core\Model\Store */
            $store = $this->_storeManager->getStore($item['store_id']);
            if ($store) {
                $timestamp = $this->_locale->storeTimeStamp($store);
                $data[] = array(
                    'website_id'   => $website->getId(),
                    'website_date' => $this->_dateTime->formatDate($timestamp, false),
                    'rate'         => $rate
                );
            }
        }

        $table = $this->_getTable('catalog_product_index_website');
        $this->_emptyTable($table);
        if ($data) {
            $write->insertMultiple($table, $data);
        }

        return $this;
    }

    /**
     * Prepare tier price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
     */
    protected function _prepareTierPriceIndex($entityIds = null)
    {
        $write = $this->_getConnection();
        $table = $this->_getTable('catalog_product_index_tier_price');
        $this->_emptyTable($table);

        $websiteExpression = $write->getCheckSql('tp.website_id = 0', 'ROUND(tp.value * cwd.rate, 4)', 'tp.value');
        $select = $write->select()
            ->from(
                array('tp' => $this->_getTable(array('catalog_product_entity', 'tier_price'))),
                array('entity_id')
            )
            ->join(
                array('cg' => $this->_getTable('customer_group')),
                'tp.all_groups = 1 OR (tp.all_groups = 0 AND tp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id')
            )
            ->join(
                array('cw' => $this->_getTable('core_website')),
                'tp.website_id = 0 OR tp.website_id = cw.website_id',
                array('website_id')
            )
            ->join(
                array('cwd' => $this->_getTable('catalog_product_index_website')),
                'cw.website_id = cwd.website_id',
                array()
            )
            ->where('cw.website_id != 0')
            ->columns(new \Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('tp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('tp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Prepare group price index table
     *
     * @param int|array $entityIds the entity ids limitation
     * @return \Magento\Catalog\Model\Indexer\Product\Price\AbstractAction
     */
    protected function _prepareGroupPriceIndex($entityIds = null)
    {
        $write = $this->_getConnection();
        $table = $this->_getTable('catalog_product_index_group_price');
        $this->_emptyTable($table);

        $websiteExpression = $write->getCheckSql('gp.website_id = 0', 'ROUND(gp.value * cwd.rate, 4)', 'gp.value');
        $select = $write->select()
            ->from(
                array('gp' => $this->_getTable(array('catalog_product_entity', 'group_price'))),
                array('entity_id')
            )
            ->join(
                array('cg' => $this->_getTable('customer_group')),
                'gp.all_groups = 1 OR (gp.all_groups = 0 AND gp.customer_group_id = cg.customer_group_id)',
                array('customer_group_id')
            )
            ->join(
                array('cw' => $this->_getTable('core_website')),
                'gp.website_id = 0 OR gp.website_id = cw.website_id',
                array('website_id')
            )
            ->join(
                array('cwd' => $this->_getTable('catalog_product_index_website')),
                'cw.website_id = cwd.website_id',
                array()
            )
            ->where('cw.website_id != 0')
            ->columns(new \Zend_Db_Expr("MIN({$websiteExpression})"))
            ->group(array('gp.entity_id', 'cg.customer_group_id', 'cw.website_id'));

        if (!empty($entityIds)) {
            $select->where('gp.entity_id IN(?)', $entityIds);
        }

        $query = $select->insertFromSelect($table);
        $write->query($query);

        return $this;
    }

    /**
     * Retrieve price indexers per product type
     *
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Price\PriceInterface[]
     */
    public function getTypeIndexers()
    {
        if (is_null($this->_indexers)) {
            $this->_indexers = array();
            $types = $this->_catalogProductType->getTypesByPriority();
            foreach ($types as $typeId => $typeInfo) {
                if (isset($typeInfo['price_indexer'])) {
                    $modelName = $typeInfo['price_indexer'];
                } else {
                    $modelName = $this->_defaultPriceIndexer;
                }
                $isComposite = !empty($typeInfo['composite']);
                $indexer = $this->_indexerPriceFactory->create($modelName)
                    ->setTypeId($typeId)
                    ->setIsComposite($isComposite);

                $this->_indexers[$typeId] = $indexer;
            }
        }

        return $this->_indexers;
    }

    /**
     * Retrieve Price indexer by Product Type
     *
     * @param string $productTypeId
     * @return \Magento\Catalog\Model\Resource\Product\Indexer\Price\PriceInterface
     * @throws \Magento\Catalog\Exception
     */
    protected function _getIndexer($productTypeId)
    {
        $this->getTypeIndexers();
        if (!isset($this->_indexers[$productTypeId])) {
            throw new \Magento\Catalog\Exception(__('Unsupported product type "%s".', $productTypeId));
        }
        return $this->_indexers[$productTypeId];
    }

    /**
     * Copy data from source table of read adapter to destination table of index adapter
     *
     * @param string $sourceTable
     * @param string $destTable
     * @param null $where
     */
    protected function _insertFromTable($sourceTable, $destTable, $where = null)
    {
        $connection = $this->_getConnection();
        $sourceColumns = array_keys($connection->describeTable($sourceTable));
        $targetColumns = array_keys($connection->describeTable($destTable));
        $select = $connection->select()->from($sourceTable, $sourceColumns);
        if ($where) {
            $select->where($where);
        }
        $query = $connection->insertFromSelect($select, $destTable, $targetColumns,
            \Magento\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE);
        $connection->query($query);
    }

    /**
     * Set or get what either "_idx" or "_tmp" suffixed temporary index table need to use
     *
     * @param bool $value
     * @return bool
     */
    protected function _useIdxTable($value = null)
    {
        if (!is_null($value)) {
            $this->_useIdxTable = (bool)$value;
        }
        return $this->_useIdxTable;
    }

    /**
     * Retrieve temporary index table name
     *
     * @return string
     */
    protected function _getIdxTable()
    {
        if ($this->_useIdxTable()) {
            return $this->_getTable('catalog_product_index_price_idx');
        }
        return $this->_getTable('catalog_product_index_price_tmp');
    }

    /**
     * Removes all data from the table
     *
     * @param string $table
     */
    protected function _emptyTable($table)
    {
        $connection = $this->_getConnection();
        if ($connection->getTransactionLevel() == 0) {
            $connection->truncateTable($table);
        } else {
            $connection->delete($table);
        }
    }
}
