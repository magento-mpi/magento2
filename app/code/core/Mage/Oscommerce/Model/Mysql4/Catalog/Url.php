<?php

class Mage_Oscommerce_Model_Mysql4_Catalog_Url extends Mage_Catalog_Model_Resource_Eav_Mysql4_Url 
{
    protected function _getCategories($categoryIds, $storeId = null, $path = null)
    {
        $categories = array();

        if (!is_array($categoryIds)) {
            $categoryIds = array($categoryIds);
        }

        $select = $this->_getWriteAdapter()->select()
            ->from($this->getTable('catalog/category'), array('entity_id', 'parent_id', 'is_active', 'path'));
        if (is_null($path)) {
            $select->where('entity_id IN(?)', $categoryIds);
        }
        else {
            $select->where('path LIKE ?', $path . '%')
                ->order('path');
        }

        if (!is_null($storeId)) {
            $rootCategoryPath = $this->getStores($storeId)->getRootCategoryPath();
            $rootCategoryPathLength = strlen($rootCategoryPath);
        }

        $rowSet = $this->_getWriteAdapter()->fetchAll($select);
        foreach ($rowSet as $row) {
            if (!is_null($storeId) && substr($row['path'], 0, $rootCategoryPathLength) != $rootCategoryPath) {
                continue;
            }

            $category = new Varien_Object($row);
            $category->setIdFieldName('entity_id');
            $category->setStoreId($storeId);
            $this->_prepareCategoryParentId($category);

            $categories[$category->getId()] = $category;
        }
        unset($rowSet);

        if (!is_null($storeId) && $categories) {
            foreach (array('name', 'url_key', 'url_path') as $attributeCode) {
                $attributes = $this->_getCategoryAttribute($attributeCode, array_keys($categories), $category->getStoreId());
                foreach ($attributes as $categoryId => $attributeValue) {
                    $categories[$categoryId]->setData($attributeCode, $attributeValue);
                }
            }
        }

        return $categories;
    }
}