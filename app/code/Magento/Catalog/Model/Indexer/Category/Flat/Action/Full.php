<?php
/**
* {license_notice}
*
* @copyright   {copyright}
* @license     {license_link}
*/
namespace Magento\Catalog\Model\Indexer\Category\Flat\Action;

class Full extends \Magento\Catalog\Model\Indexer\Category\Flat\AbstractAction
{
    /**
     * Suffix for table to show it is temporary
     */
    const TEMPORARY_TABLE_SUFFIX = '_tmp';

    /**
     * Suffix for table to show it is old
     */
    const OLD_TABLE_SUFFIX = '_old';

    /**
     * Loaded
     *
     * @var boolean
     */
    protected $_loaded = false;

    /**
     * Nodes
     *
     * @var array
     */
    protected $_nodes = array();

    /**
     * Inactive categories ids
     *
     * @var array
     */
    protected $_inactiveCategoryIds = null;

    /**
     * Store flag which defines if Catalog Category Flat Data has been initialized
     *
     * @var bool|null
     */
    protected $_isBuilt = null;

    /**
     * Whether table changes are allowed
     *
     * @var bool
     */
    protected $allowTableChanges = true;

    /**
     * @var \Magento\Catalog\Model\Indexer\Category\Flat\Config
     */
    protected $config;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Indexer\Category\Flat\Config $config
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Indexer\Category\Flat\Config $config,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper
    ) {
        parent::__construct($resource, $storeManager, $resourceHelper);
        $this->config = $config;
    }

    /**
     * Add suffix to table name to show it is
     * temporary
     *
     * @param string $tableName
     * @return string
     */
    protected function addTemporaryTableSuffix($tableName)
    {
        return $tableName . self::TEMPORARY_TABLE_SUFFIX;
    }

    /**
     * Add suffix to table name to show it is old
     *
     * @param string $tableName
     * @return string
     */
    protected function addOldTableSuffix($tableName)
    {
        return $tableName . self::OLD_TABLE_SUFFIX;
    }

    /**
     * Refresh all entities
     *
     * @return Full
     * @throws \Magento\Core\Exception
     */
    public function execute()
    {
        if (!$this->config->isFlatEnabled()) {
            return $this;
        }

        try {
            $this->reindexAll();
        } catch (Exception $e) {
            throw new \Magento\Core\Exception($e->getMessage(), $e->getCode(), $e);
        }
        return $this;
    }

    /**
     * Populate category flat tables with data
     *
     * @param array $stores
     * @return Full
     */
    protected function populateFlatTables($stores)
    {
        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;
        $categories = array();
        $categoriesIds = array();
        /* @var $store \Magento\Core\Model\Store */
        foreach ($stores as $store) {

            if (!isset($categories[$store->getRootCategoryId()])) {
                $select = $this->getWriteAdapter()->select()
                    ->from($this->getTable('catalog_category_entity'))
                    ->where('path = ?', (string)$rootId)
                    ->orWhere('path = ?', "{$rootId}/{$store->getRootCategoryId()}")
                    ->orWhere('path LIKE ?', "{$rootId}/{$store->getRootCategoryId()}/%");
                $categories[$store->getRootCategoryId()] = $this->getWriteAdapter()->fetchAll($select);
                $categoriesIds[$store->getRootCategoryId()] = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    $categoriesIds[$store->getRootCategoryId()][] = $category['entity_id'];
                }
            }
            /** @TODO Do something with chunks */
            $categoriesIdsChunks = array_chunk($categoriesIds[$store->getRootCategoryId()], 500);
            foreach ($categoriesIdsChunks as $categoriesIdsChunk) {
                $attributesData = $this->getAttributeValues($categoriesIdsChunk, $store->getId());
                $data = array();
                foreach ($categories[$store->getRootCategoryId()] as $category) {
                    if (!isset($attributesData[$category['entity_id']])) {
                        continue;
                    }
                    $category['store_id'] = $store->getId();
                    $data[] = $this->prepareValuesToInsert(
                        array_merge($category, $attributesData[$category['entity_id']])
                    );
                }
                $this->getWriteAdapter()->insertMultiple(
                    $this->addTemporaryTableSuffix($this->getMainStoreTable($store->getId())),
                    $data
                );
            }
        }

        return $this;
    }

    /**
     * Create table and add attributes as fields for specified store.
     * This routine assumes that DDL operations are allowed
     *
     * @param int $store
     * @return Full
     */
    protected function createTable($store)
    {
        $temporaryTable = $this->addTemporaryTableSuffix($this->getMainStoreTable($store));
        $activeTable    = $this->getMainStoreTable($store);
        $table  = $this->getFlatTableStructure($temporaryTable, $activeTable);
        $this->getWriteAdapter()->dropTable($temporaryTable);
        $this->dropOldForeignKeys($activeTable);
        $this->getWriteAdapter()->createTable($table);

        return $this;
    }

    /**
     * Create category flat tables and add attributes as fields.
     * Tables are created only if DDL operations are allowed
     *
     * @param array $stores if empty, create tables for all stores of the application
     * @return Full
     */
    protected function createTables($stores = array())
    {
        if ($this->getWriteAdapter()->getTransactionLevel() > 0) {
            return $this;
        }
        if (empty($stores)) {
            $stores = $this->storeManager->getStores();
        }
        foreach ($stores as $store) {
            $this->createTable($store->getId());
        }
        return $this;
    }

    /**
     * Switch table (temporary becomes active, old active will be dropped)
     *
     * @param array $stores
     * @return Full
     */
    protected function switchTables($stores)
    {
        /** @var $store Mage_Core_Model_Store */
        foreach ($stores as $store) {
            $activeTableName = $this->getMainStoreTable($store->getId());
            $temporaryTableName = $this->addTemporaryTableSuffix($this->getMainStoreTable($store->getId()));
            $oldTableName = $this->addOldTableSuffix($this->getMainStoreTable($store->getId()));

            //switch tables
            $tablesToRename = array();
            if ($this->getWriteAdapter()->isTableExists($activeTableName)) {
                $tablesToRename[] = array(
                    'oldName' => $activeTableName,
                    'newName' => $oldTableName
                );
            }

            $tablesToRename[] = array(
                'oldName' => $temporaryTableName,
                'newName' => $activeTableName
            );

            /** @TODO Need to realize method renameTablesBatch() */
            foreach ($tablesToRename as $tableToRename) {
                $this->getWriteAdapter()->renameTable($tableToRename['oldName'], $tableToRename['newName']);
            }

            //delete inactive table
            $tableToDelete = $oldTableName;

            if ($this->getWriteAdapter()->isTableExists($tableToDelete)) {
                $this->getWriteAdapter()->dropTable($tableToDelete);
            }
        }

        return $this;
    }

    /**
     * Transactional rebuild flat data from eav
     *
     * @return Refresh
     */
    protected function reindexAll()
    {
        $this->createTables();

        if ($this->allowTableChanges) {
            $this->allowTableChanges = false;
        }
        $stores = $this->storeManager->getStores();
        $this->populateFlatTables($stores);
        $this->switchTables($stores);

        $this->allowTableChanges = true;

        return $this;
    }
}
