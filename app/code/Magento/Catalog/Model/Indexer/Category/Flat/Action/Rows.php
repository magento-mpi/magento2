<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Model\Indexer\Category\Flat\Action;

class Rows extends \Magento\Catalog\Model\Indexer\Category\Flat\AbstractAction
{
    /**
     * @var \Magento\Catalog\Model\CategoryFactory
     */
    protected $categoryFactory;

    /**
     * @param \Magento\App\Resource $resource
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Resource\Helper $resourceHelper
     * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
     */
    public function __construct(
        \Magento\App\Resource $resource,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Resource\Helper $resourceHelper,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        parent::__construct($resource, $storeManager, $resourceHelper);
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Refresh entities index
     *
     * @param array $entityIds
     * @return Rows
     */
    public function reindex($entityIds = array())
    {
        $stores = $this->storeManager->getStores();

        /* @var $category \Magento\Catalog\Model\Category */
        $category = $this->categoryFactory->create();

        /* @var $store \Magento\Core\Model\Store */
        foreach ($stores as $store) {
            if (!$this->getWriteAdapter()->isTableExists($this->getMainStoreTable($store->getId()))) {
                $tableName = $this->getMainStoreTable($store->getId());
                $table     = $this->getFlatTableStructure($tableName);
                $this->dropOldForeignKeys($tableName);
                $this->getWriteAdapter()->createTable($table);
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

                    if ($category->load($categoryId)->getId()) {
                        $data[] = $this->prepareValuesToInsert(
                            array_merge(
                                $category->getData(),
                                $attributesData[$categoryId],
                                array('store_id' => $store->getId())
                            )
                        );
                    }
                }
                foreach ($data as $row) {
                    $updateFields = array();
                    foreach (array_keys($row) as $key) {
                        $updateFields[$key] = $key;
                    }
                    $this->getWriteAdapter()->insertOnDuplicate(
                        $this->getMainStoreTable($store->getId()),
                        $row,
                        $updateFields
                    );
                }
            }
            $this->deleteNonStoreCategories($store);
        }

        /** @TODO evant catalog_category_flat_partial_reindex was here */

        return $this;
    }

    /**
     * Delete non stores categories
     *
     * @param \Magento\Core\Model\Store $store
     * @return void
     */
    protected function deleteNonStoreCategories($store)
    {
        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;

        $rootIdExpr = $this->getWriteAdapter()->quote((string)$rootId);
        $rootCatIdExpr = $this->getWriteAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}");
        $catIdExpr = $this->getWriteAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}/%");

        $select = $this->getWriteAdapter()->select()
            ->from(array('cf' => $this->getMainStoreTable($store->getId())))
            ->joinLeft(
                array('ce' => $this->getWriteAdapter()->getTableName('catalog_category_entity')),
                'cf.path = ce.path',
                array()
            )
            ->where("cf.path = {$rootIdExpr} OR cf.path = {$rootCatIdExpr} OR cf.path like {$catIdExpr}")
            ->where('ce.entity_id IS NULL');

        $sql = $select->deleteFromSelect('cf');
        $this->getWriteAdapter()->query($sql);
    }

    /**
     * Filter category ids by store
     *
     * @param array $ids
     * @param \Magento\Core\Model\Store $store
     * @return array
     */
    protected function filterIdsByStore($ids, $store)
    {
        $rootId = \Magento\Catalog\Model\Category::TREE_ROOT_ID;

        $rootIdExpr = $this->getReadAdapter()->quote((string)$rootId);
        $rootCatIdExpr = $this->getReadAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}");
        $catIdExpr = $this->getReadAdapter()->quote("{$rootId}/{$store->getRootCategoryId()}/%");

        $select = $this->getReadAdapter()->select()
            ->from($this->getReadAdapter()->getTableName('catalog_category_entity'), array('entity_id'))
            ->where("path = {$rootIdExpr} OR path = {$rootCatIdExpr} OR path like {$catIdExpr}")
            ->where('entity_id IN (?)', $ids);

        $resultIds = array();
        foreach ($this->getReadAdapter()->fetchAll($select) as $category) {
            $resultIds[] = $category['entity_id'];
        }
        return $resultIds;
    }
}
