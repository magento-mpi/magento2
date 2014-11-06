<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Action;

use Magento\Catalog\Model\CategoryRepository;
use Magento\Framework\Exception\NoSuchEntityException;

class Rows extends \Magento\Catalog\Model\Indexer\Category\Flat\AbstractAction
{
    /**
     * @var CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @param \Magento\Framework\App\Resource $resource
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(
        \Magento\Framework\App\Resource $resource,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        CategoryRepository $categoryRepository
    ) {
        $this->categoryRepository = $categoryRepository;
        parent::__construct($resource, $storeManager, $resourceHelper);
    }

    /**
     * Return index table name
     *
     * @param \Magento\Store\Model\Store $store
     * @param bool $useTempTable
     * @return string
     */
    protected function getTableNameByStore(\Magento\Store\Model\Store $store, $useTempTable)
    {
        $tableName = $this->getMainStoreTable($store->getId());
        return $useTempTable ? $this->addTemporaryTableSuffix($tableName) : $tableName;
    }

    /**
     * Refresh entities index
     *
     * @param int[] $entityIds
     * @param bool $useTempTable
     * @return Rows
     */
    public function reindex(array $entityIds = array(), $useTempTable = false)
    {
        $stores = $this->storeManager->getStores();

        /* @var $store \Magento\Store\Model\Store */
        foreach ($stores as $store) {
            $tableName = $this->getTableNameByStore($store, $useTempTable);

            if (!$this->getWriteAdapter()->isTableExists($tableName)) {
                continue;
            }

            /** @TODO Do something with chunks */
            $categoriesIdsChunks = array_chunk($entityIds, 500);
            foreach ($categoriesIdsChunks as $categoriesIdsChunk) {

                $categoriesIdsChunk = $this->filterIdsByStore($categoriesIdsChunk, $store);

                $attributesData = $this->getAttributeValues($categoriesIdsChunk, $store->getId());
                $data = array();
                foreach ($categoriesIdsChunk as $categoryId) {
                    if (!isset($attributesData[$categoryId])) {
                        continue;
                    }

                    try {
                        $category = $this->categoryRepository->get($categoryId);
                    } catch (NoSuchEntityException $e) {
                        continue;
                    }

                    $data[] = $this->prepareValuesToInsert(
                        array_merge(
                            $category->getData(),
                            $attributesData[$categoryId],
                            array('store_id' => $store->getId())
                        )
                    );
                }

                foreach ($data as $row) {
                    $updateFields = array();
                    foreach (array_keys($row) as $key) {
                        $updateFields[$key] = $key;
                    }
                    $this->getWriteAdapter()->insertOnDuplicate($tableName, $row, $updateFields);
                }
            }
            $this->deleteNonStoreCategories($store, $useTempTable);
        }

        return $this;
    }

    /**
     * Delete non stores categories
     *
     * @param \Magento\Store\Model\Store $store
     * @param bool $useTempTable
     * @return void
     */
    protected function deleteNonStoreCategories(\Magento\Store\Model\Store $store, $useTempTable)
    {
        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;

        $rootIdExpr = $this->getWriteAdapter()->quote((string)$rootId);
        $rootCatIdExpr = $this->getWriteAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}");
        $catIdExpr = $this->getWriteAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}/%");

        /** @var \Magento\Framework\DB\Select $select */
        $select = $this->getWriteAdapter()->select()->from(
            array('cf' => $this->getTableNameByStore($store, $useTempTable))
        )->joinLeft(
            array('ce' => $this->getTableName('catalog_category_entity')),
            'cf.path = ce.path',
            array()
        )->where(
            "cf.path = {$rootIdExpr} OR cf.path = {$rootCatIdExpr} OR cf.path like {$catIdExpr}"
        )->where(
            'ce.entity_id IS NULL'
        );

        $sql = $select->deleteFromSelect('cf');
        $this->getWriteAdapter()->query($sql);
    }

    /**
     * Filter category ids by store
     *
     * @param int[] $ids
     * @param \Magento\Store\Model\Store $store
     * @return int[]
     */
    protected function filterIdsByStore(array $ids, $store)
    {
        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;

        $rootIdExpr = $this->getReadAdapter()->quote((string)$rootId);
        $rootCatIdExpr = $this->getReadAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}");
        $catIdExpr = $this->getReadAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}/%");

        $select = $this->getReadAdapter()->select()->from(
            $this->getTableName('catalog_category_entity'),
            array('entity_id')
        )->where(
            "path = {$rootIdExpr} OR path = {$rootCatIdExpr} OR path like {$catIdExpr}"
        )->where(
            'entity_id IN (?)',
            $ids
        );

        $resultIds = array();
        foreach ($this->getReadAdapter()->fetchAll($select) as $category) {
            $resultIds[] = $category['entity_id'];
        }
        return $resultIds;
    }
}
