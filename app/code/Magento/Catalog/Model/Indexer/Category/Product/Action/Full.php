<?php
/**
 * {license_notice}
 *
 * @copyright {copyright}
 * @license   {license_link}
 */

namespace Magento\Catalog\Model\Indexer\Category\Product\Action;

class Full
{
    /**
     * Chunk size
     */
    const RANGE_CATEGORY_STEP = 500;

    /**
     * Chunk size for product
     */
    const RANGE_PRODUCT_STEP = 1000000;

    /**
     * Catalog category index table name
     */
    const MAIN_INDEX_TABLE = 'catalog_category_product_index';

    /**
     * Cached non anchor categories select by store id
     *
     * @var \Magento\DB\Select[]
     */
    protected $nonAnchorSelects = [];

    /**
     * Cached anchor categories select by store id
     *
     * @var \Magento\DB\Select[]
     */
    protected $anchorSelects = [];

    /**
     * Cached all product select by store id
     *
     * @var \Magento\DB\Select[]
     */
    protected $productsSelects = [];

    /**
     * Category path by id
     *
     * @var string[]
     */
    protected $categoryPath = [];

    /**
     * @var \Magento\App\Resource
     */
    protected $resource;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Catalog\Model\Config
     */
    protected $config;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Config $config
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Config $config
    ) {
        $this->resource = $resource;
        $this->storeManager = $storeManager;
        $this->config = $config;
    }

    /**
     * Run full reindex
     *
     * @return $this
     */
    public function execute()
    {
        $this->createTmpTable();

        foreach ($this->storeManager->getStores() as $store) {
            if ($this->getPathFromCategoryId($store->getRootCategoryId())) {
                $this->reindexNonAnchorCategories($store);
                $this->reindexAnchorCategories($store);
                $this->reindexRootCategory($store);
            }
        }
        $this->publishData();
        $this->removeUnnecessaryData();
        $this->clearTmpData();

        return $this;
    }

    /**
     * Return validated table name
     *
     * @param string|string[] $table
     * @return string
     */
    protected function getTable($table)
    {
        return $this->resource->getTableName($table);
    }

    /**
     * Return main index table name
     *
     * @return string
     */
    protected function getMainTable()
    {
        return $this->getTable(self::MAIN_INDEX_TABLE);
    }

    /**
     * Return temporary index table name
     *
     * @return string
     */
    protected function getMainTmpTable()
    {
        return $this->getTable(self::MAIN_INDEX_TABLE . '_tmp');
    }

    /**
     * Retrieve connection for read data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getReadAdapter()
    {
        $writeAdapter = $this->getWriteAdapter();
        if ($writeAdapter && $writeAdapter->getTransactionLevel() > 0) {
            // if transaction is started we should use write connection for reading
            return $writeAdapter;
        }
        return $this->resource->getConnection('read');
    }

    /**
     * Retrieve connection for write data
     *
     * @return \Magento\DB\Adapter\AdapterInterface
     */
    protected function getWriteAdapter()
    {
        return $this->resource->getConnection('write');
    }

    /**
     * Create temporary index table
     */
    protected function createTmpTable()
    {
        $table = $this->getWriteAdapter()
            ->newTable($this->getMainTmpTable())
            ->addColumn(
                'category_id',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Category ID'
            )
            ->addColumn(
                'product_id',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Product ID'
            )
            ->addColumn(
                'position',
                \Magento\DB\Ddl\Table::TYPE_INTEGER,
                null,
                [
                    'unsigned'  => false,
                    'nullable'  => true,
                    'default'   => null,
                ],
                'Position'
            )
            ->addColumn(
                'is_parent',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'default'   => '0',
                ],
                'Is Parent'
            )
            ->addColumn(
                'store_id',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                    'primary'   => true,
                    'default'   => '0',
                ],
                'Store ID'
            )
            ->addColumn(
                'visibility',
                \Magento\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [
                    'unsigned'  => true,
                    'nullable'  => false,
                ],
                'Visibility'
            )
            ->setComment('Catalog Category Product Index Tmp');

        $this->getWriteAdapter()->dropTable($this->getMainTmpTable());
        $this->getWriteAdapter()->createTable($table);
    }

    /**
     * Return select for remove unnecessary data
     *
     * @return \Magento\DB\Select
     */
    protected function getSelectUnnecessaryData()
    {
        return $this->getWriteAdapter()->select()
            ->from($this->getMainTable(), [])
            ->joinLeft(
                ['t' => $this->getMainTmpTable()],
                $this->getMainTable() . '.category_id = t.category_id AND '
                . $this->getMainTable() . '.store_id = t.store_id AND '
                . $this->getMainTable() . '.product_id = t.product_id',
                []
            )
            ->where('t.category_id IS NULL');
    }

    /**
     * Remove unnecessary data
     */
    protected function removeUnnecessaryData()
    {
        $this->getWriteAdapter()->query(
            $this->getWriteAdapter()->deleteFromSelect(
                $this->getSelectUnnecessaryData(), $this->getMainTable()
            )
        );
    }

    /**
     * Publish data from tmp to index
     */
    protected function publishData()
    {
        $select = $this->getWriteAdapter()->select()
            ->from($this->getMainTmpTable());

        $queries = $this->prepareSelectsByRange($select, 'category_id');

        foreach ($queries as $query) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $query,
                    $this->getMainTable(),
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_ON_DUPLICATE
                )
            );
        }
    }

    /**
     * Clear all index data
     */
    protected function clearTmpData()
    {
        $this->getWriteAdapter()->dropTable($this->getMainTmpTable());
    }

    /**
     * Return category path by id
     *
     * @param int $categoryId
     * @return string
     */
    protected function getPathFromCategoryId($categoryId)
    {
        if (!isset($this->categoryPath[$categoryId])) {
            $this->categoryPath[$categoryId] = $this->getReadAdapter()->fetchOne(
                $this->getReadAdapter()->select()
                    ->from($this->getTable('catalog_category_entity'), ['path'])
                    ->where('entity_id = ?', $categoryId)
            );
        }
        return $this->categoryPath[$categoryId];
    }

    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getNonAnchorCategoriesSelect(\Magento\Core\Model\Store $store)
    {
        if (!isset($this->nonAnchorSelects[$store->getId()])) {
            $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'status'
            )->getId();
            $visibilityAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'visibility'
            )->getId();

            $rootPath = $this->getPathFromCategoryId($store->getRootCategoryId());

            $select = $this->getWriteAdapter()->select()
                ->from(['cc' => $this->getTable('catalog_category_entity')], [])
                ->joinInner(
                    ['ccp' => $this->getTable('catalog_category_product')],
                    'ccp.category_id = cc.entity_id',
                    []
                )
                ->joinInner(
                    ['cpw' => $this->getTable('catalog_product_website')],
                    'cpw.product_id = ccp.product_id',
                    []
                )
                ->joinInner(
                    ['cpsd' => $this->getTable('catalog_product_entity_int')],
                    'cpsd.entity_id = ccp.product_id AND cpsd.store_id = 0'
                        . ' AND cpsd.attribute_id = ' . $statusAttributeId,
                    []
                )
                ->joinLeft(
                    ['cpss' => $this->getTable('catalog_product_entity_int')],
                    'cpss.entity_id = ccp.product_id AND cpss.attribute_id = cpsd.attribute_id'
                        . ' AND cpss.store_id = ' . $store->getId(),
                    []
                )
                ->joinInner(
                    ['cpvd' => $this->getTable('catalog_product_entity_int')],
                    'cpvd.entity_id = ccp.product_id AND cpvd.store_id = 0'
                        . ' AND cpvd.attribute_id = ' . $visibilityAttributeId,
                    []
                )
                ->joinLeft(
                    ['cpvs' => $this->getTable('catalog_product_entity_int')],
                    'cpvs.entity_id = ccp.product_id AND cpvs.attribute_id = cpvd.attribute_id'
                        . ' AND cpvs.store_id = ' . $store->getId(),
                    []
                )
                ->where(
                    'cc.path LIKE ' . $this->getWriteAdapter()->getConcatSql(
                        [
                            $this->getWriteAdapter()->quote($rootPath),
                            $this->getWriteAdapter()->quote('/%'),
                        ]
                    )
                )
                ->where('cpw.website_id = ?', $store->getWebsiteId())
                ->where(
                    $this->getWriteAdapter()->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                    \Magento\Catalog\Model\Product\Status::STATUS_ENABLED
                )
                ->where(
                    $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                    [
                        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG,
                    ]
                )
                ->columns(
                    [
                        'category_id' => 'cc.entity_id',
                        'product_id'  => 'ccp.product_id',
                        'position'    => 'ccp.position',
                        'is_parent'   => new \Zend_Db_Expr('1'),
                        'store_id'    => new \Zend_Db_Expr($store->getId()),
                        'visibility'  => new \Zend_Db_Expr(
                                $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value')),
                    ]
                );

            $this->nonAnchorSelects[$store->getId()] = $select;
        }

        return $this->nonAnchorSelects[$store->getId()];
    }

    /**
     * Check whether select ranging is needed
     *
     * @return bool
     */
    protected function isRangingNeeded() {
        return true;
    }

    /**
     * Return selects cut by min and max
     *
     * @param \Magento\DB\Select $select
     * @param string $field
     * @param int $range
     * @return \Magento\DB\Select[]
     */
    protected function prepareSelectsByRange(\Magento\DB\Select $select, $field, $range = self::RANGE_CATEGORY_STEP)
    {
        return $this->isRangingNeeded()
            ? $this->getWriteAdapter()->selectsByRange($field, $select, $range)
            : array($select);
    }

    /**
     * Reindex products of non anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     */
    protected function reindexNonAnchorCategories(\Magento\Core\Model\Store $store)
    {
        $selects = $this->prepareSelectsByRange($this->getNonAnchorCategoriesSelect($store), 'entity_id');
        foreach ($selects as $select) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $select,
                    $this->getMainTmpTable(),
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility']
                )
            );
        }
    }

    /**
     * Check if anchor select isset
     *
     * @param \Magento\Core\Model\Store $store
     * @return bool
     */
    protected function hasAnchorSelect(\Magento\Core\Model\Store $store)
    {
        return isset($this->anchorSelects[$store->getId()]);
    }

    /**
     * Create anchor select
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function createAnchorSelect(\Magento\Core\Model\Store $store)
    {
        $isAnchorAttributeId = $this->config->getAttribute(\Magento\Catalog\Model\Category::ENTITY, 'is_anchor')
            ->getId();
        $statusAttributeId = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'status')
            ->getId();
        $visibilityAttributeId = $this->config->getAttribute(\Magento\Catalog\Model\Product::ENTITY, 'visibility')
            ->getId();
        $rootCatIds = explode('/', $this->getPathFromCategoryId($store->getRootCategoryId()));
        array_pop($rootCatIds);
        return $this->getWriteAdapter()->select()
            ->from(['cc' => $this->getTable('catalog_category_entity')], [])
            ->joinInner(
                ['cc2' => $this->getTable('catalog_category_entity')],
                'cc2.path LIKE ' . $this->getWriteAdapter()->getConcatSql(
                    [
                        $this->getWriteAdapter()->quoteIdentifier('cc.path'),
                        $this->getWriteAdapter()->quote('/%')
                    ]
                ) . ' AND cc.entity_id NOT IN (' . implode(',', $rootCatIds) . ')',
                []
            )
            ->joinInner(
                ['ccp' => $this->getTable('catalog_category_product')],
                'ccp.category_id = cc2.entity_id',
                []
            )
            ->joinInner(
                ['cpw' => $this->getTable('catalog_product_website')],
                'cpw.product_id = ccp.product_id',
                []
            )
            ->joinInner(
                ['cpsd' => $this->getTable('catalog_product_entity_int')],
                'cpsd.entity_id = ccp.product_id AND cpsd.store_id = 0'
                . ' AND cpsd.attribute_id = ' . $statusAttributeId,
                []
            )
            ->joinLeft(
                ['cpss' => $this->getTable('catalog_product_entity_int')],
                'cpss.entity_id = ccp.product_id AND cpss.attribute_id = cpsd.attribute_id'
                . ' AND cpss.store_id = ' . $store->getId(),
                []
            )
            ->joinInner(
                ['cpvd' => $this->getTable('catalog_product_entity_int')],
                'cpvd.entity_id = ccp.product_id AND cpvd.store_id = 0'
                . ' AND cpvd.attribute_id = ' . $visibilityAttributeId,
                []
            )
            ->joinLeft(
                ['cpvs' => $this->getTable('catalog_product_entity_int')],
                'cpvs.entity_id = ccp.product_id AND cpvs.attribute_id = cpvd.attribute_id '
                . 'AND cpvs.store_id = ' . $store->getId(),
                []
            )
            ->joinInner(
                ['ccad' => $this->getTable('catalog_category_entity_int')],
                'ccad.entity_id = cc.entity_id AND ccad.store_id = 0'
                . ' AND ccad.attribute_id = ' . $isAnchorAttributeId,
                []
            )
            ->joinLeft(
                ['ccas' => $this->getTable('catalog_category_entity_int')],
                'ccas.entity_id = cc.entity_id AND ccas.attribute_id = ccad.attribute_id'
                . ' AND ccas.store_id = ' . $store->getId(),
                []
            )
            ->where('cpw.website_id = ?', $store->getWebsiteId())
            ->where(
                $this->getWriteAdapter()->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                \Magento\Catalog\Model\Product\Status::STATUS_ENABLED
            )
            ->where(
                $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                [
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                    \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
                ]
            )
            ->where($this->getWriteAdapter()->getIfNullSql('ccas.value', 'ccad.value') . ' = ?', 1)
            ->columns(
                [
                    'category_id' => 'cc.entity_id',
                    'product_id'  => 'ccp.product_id',
                    'position'    => new \Zend_Db_Expr('ccp.position + 10000'),
                    'is_parent'   => new \Zend_Db_Expr('0'),
                    'store_id'    => new \Zend_Db_Expr($store->getId()),
                    'visibility'  => new \Zend_Db_Expr(
                            $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value')),
                ]
            );
    }

    /**
     * Retrieve select for reindex products of non anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getAnchorCategoriesSelect(\Magento\Core\Model\Store $store)
    {
        if (!$this->hasAnchorSelect($store)) {
            $this->anchorSelects[$store->getId()] = $this->createAnchorSelect($store);
        }
        return $this->anchorSelects[$store->getId()];
    }

    /**
     * Reindex products of anchor categories
     *
     * @param \Magento\Core\Model\Store $store
     */
    protected function reindexAnchorCategories(\Magento\Core\Model\Store $store)
    {
        $selects = $this->prepareSelectsByRange($this->getAnchorCategoriesSelect($store), 'entity_id');

        foreach ($selects as $select) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $select,
                    $this->getMainTmpTable(),
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_IGNORE
                )
            );
        }
    }

    /**
     * Get select for all products
     *
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\DB\Select
     */
    protected function getAllProducts(\Magento\Core\Model\Store $store)
    {
        if (!isset($this->productsSelects[$store->getId()])) {
            $statusAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'status'
            )->getId();
            $visibilityAttributeId = $this->config->getAttribute(
                \Magento\Catalog\Model\Product::ENTITY, 'visibility'
            )->getId();

            $select = $this->getWriteAdapter()->select()
                ->from(['cp' => $this->getTable('catalog_product_entity')], [])
                ->joinInner(
                    ['cpw' => $this->getTable('catalog_product_website')],
                    'cpw.product_id = cp.entity_id',
                    []
                )
                ->joinInner(
                    ['cpsd' => $this->getTable('catalog_product_entity_int')],
                    'cpsd.entity_id = cp.entity_id AND cpsd.store_id = 0'
                        . ' AND cpsd.attribute_id = ' . $statusAttributeId,
                    []
                )
                ->joinLeft(
                    ['cpss' => $this->getTable('catalog_product_entity_int')],
                    'cpss.entity_id = cp.entity_id AND cpss.attribute_id = cpsd.attribute_id'
                        . ' AND cpss.store_id = ' . $store->getId(),
                    []
                )
                ->joinInner(
                    ['cpvd' => $this->getTable('catalog_product_entity_int')],
                    'cpvd.entity_id = cp.entity_id AND cpvd.store_id = 0'
                        . ' AND cpvd.attribute_id = ' . $visibilityAttributeId,
                    []
                )
                ->joinLeft(
                    ['cpvs' => $this->getTable('catalog_product_entity_int')],
                    'cpvs.entity_id = cp.entity_id AND cpvs.attribute_id = cpvd.attribute_id '
                        . ' AND cpvs.store_id = ' . $store->getId(),
                    []
                )
                ->joinLeft(
                    ['ccp' => $this->getTable('catalog_category_product')],
                    'ccp.product_id = cp.entity_id',
                    []
                )
                ->where('cpw.website_id = ?', $store->getWebsiteId())
                ->where(
                    $this->getWriteAdapter()->getIfNullSql('cpss.value', 'cpsd.value') . ' = ?',
                    \Magento\Catalog\Model\Product\Status::STATUS_ENABLED
                )
                ->where(
                    $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value') . ' IN (?)',
                    [
                        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_BOTH,
                        \Magento\Catalog\Model\Product\Visibility::VISIBILITY_IN_CATALOG
                    ]
                )
                ->group('cp.entity_id')
                ->columns(
                    [
                        'category_id' => new \Zend_Db_Expr($store->getRootCategoryId()),
                        'product_id'  => 'cp.entity_id',
                        'position'    => new \Zend_Db_Expr(
                                $this->getWriteAdapter()->getCheckSql('ccp.product_id IS NOT NULL', 'ccp.position', '0')
                            ),
                        'is_parent'   => new \Zend_Db_Expr(
                                $this->getWriteAdapter()->getCheckSql('ccp.product_id IS NOT NULL', '0', '1')),
                        'store_id'    => new \Zend_Db_Expr($store->getId()),
                        'visibility'  => new \Zend_Db_Expr(
                                $this->getWriteAdapter()->getIfNullSql('cpvs.value', 'cpvd.value')),
                    ]
                );

            $this->productsSelects[$store->getId()] = $select;
        }

        return $this->productsSelects[$store->getId()];
    }

    /**
     * Reindex all products to root category
     *
     * @param \Magento\Core\Model\Store $store
     */
    protected function reindexRootCategory(\Magento\Core\Model\Store $store)
    {
        $selects = $this->prepareSelectsByRange($this->getAllProducts($store), 'entity_id', self::RANGE_PRODUCT_STEP);

        foreach ($selects as $select) {
            $this->getWriteAdapter()->query(
                $this->getWriteAdapter()->insertFromSelect(
                    $select,
                    $this->getMainTmpTable(),
                    ['category_id', 'product_id', 'position', 'is_parent', 'store_id', 'visibility'],
                    \Magento\DB\Adapter\AdapterInterface::INSERT_IGNORE
                )
            );
        }
    }
}
