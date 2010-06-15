<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */

/**
 * Enterprise search collection resource model
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */

class Enterprise_Search_Model_Resource_Index
    extends Mage_CatalogSearch_Model_Mysql4_Fulltext
{
/**
     * Prepare advanced index for products
     *
     * @see Mage_CatalogSearch_Model_Mysql4_Fulltext->_getSearchableProducts()
     *
     * @param array $index
     * @param int $storeId
     * @param array | null $productIds
     *
     * @return array
     */
    public function addAdvancedIndex($index, $storeId, $productIds = null)
    {
        if (is_null($productIds) || is_array($productIds)) {
            $productIds = array();
            foreach ($index as $productData) {
                $productIds[] = $productData['entity_id'];
            }
        }

        $fieldPrefix = $this->_engine->getFieldsPrefix();

        $categoriesExpr = new Zend_Db_Expr(
            $this->_getWriteAdapter()->quoteInto('GROUP_CONCAT(
                IF(is_parent = 1, category_id, \'\') SEPARATOR ?)', ' '));
        $showInCategoriesExpr = new Zend_Db_Expr(
            $this->_getWriteAdapter()->quoteInto('GROUP_CONCAT(
                IF(is_parent = 0, category_id, \'\') SEPARATOR ?)', ' '));
        $positionsExpr = new Zend_Db_Expr(
            $this->_getWriteAdapter()->quoteInto('GROUP_CONCAT(
                CONCAT(category_id, \'_\', position) SEPARATOR ?)', ' '));

        $select = $this->_getWriteAdapter()->select()
            ->from(
                array($this->getTable('catalog/category_product_index')),
                array(
                    'product_id',
                    'categories' => $categoriesExpr,
                    'show_in_categories' => $showInCategoriesExpr,
                    'positions' => $positionsExpr,
                    'visibility'))
            ->where('product_id IN (?)', $productIds)
            ->where('store_id = ?', $storeId)
            ->group('product_id');

        $additionalIndexData = array();
        foreach ($this->_getWriteAdapter()->fetchAll($select) as $data) {
            $additionalIndexData[$data['product_id']] = $data;
        }

        $select->reset()
            ->from(
                array($this->getTable('catalog/product_index_price')),
                array('entity_id', 'customer_group_id', 'website_id', 'min_price'))
            ->where('entity_id IN (?)', $productIds);

        $additionalPriceData = array();
        foreach ($this->_getWriteAdapter()->fetchAll($select) as $price) {
            $key = $fieldPrefix . 'price_' . $price['customer_group_id'] . '_' . $price['website_id'];
            $additionalPriceData[$price['entity_id']][$key] = $price['min_price'];
        }

        foreach ($index as &$productData) {
            $productId = $productData['entity_id'];
            /*
             * If there is no info about product price in
             * catalog_product_index_price table then skip it
             */
            if (!isset($additionalPriceData[$productId])) {
                continue;
            }

            $productData[$fieldPrefix . 'categories'] = array();
            foreach (explode(' ', $additionalIndexData[$productId]['categories']) as $category_id) {
                if (!empty($category_id)) {
                    $productData[$fieldPrefix . 'categories'][] = $category_id;
                }
            }

            $productData[$fieldPrefix . 'show_in_categories'] = array();
            foreach (explode(' ', $additionalIndexData[$productId]['show_in_categories']) as $category_id) {
                if (!empty($category_id)) {
                    $productData[$fieldPrefix . 'show_in_categories'][] = $category_id;
                }
            }

            foreach (explode(' ', $additionalIndexData[$productId]['positions']) as $position) {
                $categoryPosition = explode('_', $position);
                $productData[$fieldPrefix . 'position_category_' . $categoryPosition[0]] = $categoryPosition[1];
            }

            $productData[$fieldPrefix . 'visibility'] = $additionalIndexData[$productId]['visibility'];

            $productData += $additionalPriceData[$productId];
        }
        unset($additionalIndexData);
        unset($additionalPriceData);

        return $index;
    }
}
