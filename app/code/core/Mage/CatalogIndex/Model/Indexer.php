<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_CatalogIndex
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Index operation model
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Indexer extends Mage_Core_Model_Abstract
{
    protected $_indexers = array();

    protected function _construct()
    {
        $this->_loadIndexers();
        $this->_init('catalogindex/indexer');
    }

    protected function _loadIndexers()
    {
        foreach ($this->_getRegisteredIndexers() as $name=>$class) {
            $this->_indexers[$name] = Mage::getSingleton($class);
        }
    }

    protected function _getRegisteredIndexers()
    {
        $result = array();
        $indexerRegistry = Mage::getConfig()->getNode('global/catalogindex/indexer');

        foreach ($indexerRegistry->children() as $node) {
            $result[$node->getName()] = (string) $node->class;
        }
        return $result;
    }

    protected function _getIndexableAttributeCodes()
    {
        $result = array();
        foreach ($this->_indexers as $indexer) {
            $codes = $indexer->getIndexableAttributeCodes();

            if (is_array($codes))
                $result = array_merge($result, $codes);
        }
        return $result;
    }

    /**
     * Retreive store collection
     *
     * @return Mage_Core_Model_Mysql4_Store_Collection
     */
    protected function _getStores()
    {
        $stores = $this->getData('_stores');
        if (is_null($stores)) {
            $stores = array();

            $stores = Mage::getModel('core/store')->getCollection()->load();
            /* @var $stores Mage_Core_Model_Mysql4_Store_Collection */

            $stores->removeItemByKey(0);

            $this->setData('_stores', $stores);
        }
        return $stores;
    }

/*
    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
    }
*/

    public function buildEntityFilter($attributes, $values, &$filteredAttributes)
    {
        $filter = array();
        $store = Mage::app()->getStore()->getId();

        foreach ($attributes as $attribute) {
            $code = $attribute->getAttributeCode();
            if (isset($values[$code])) {
                foreach ($this->_indexers as $indexer) {
                    /* @var $indexer Mage_CatalogIndex_Model_Indexer_Abstract */
                    if ($indexer->isAttributeIndexable($attribute)) {
                        if ($values[$code]) {
                            if (isset($values[$code]['from']) && isset($values[$code]['to']) && (!$values[$code]['from'] && !$values[$code]['to']))
                                continue;
                            $table = $indexer->getResource()->getMainTable();
                            if (!isset($filter[$table])) {
                                $filter[$table] = $this->_getSelect();
                                $filter[$table]->from($table, array('entity_id'));
                            }
                            $filter[$table]->where('(attribute_id = ?', $attribute->getId());
                            if (is_array($values[$code])) {
                                if (isset($values[$code]['from']) && isset($values[$code]['to'])) {

                                    if ($values[$code]['from']) {
                                        if (!is_numeric($values[$code]['from'])) {
                                            $values[$code]['from'] = date("Y-m-d H:i:s", strtotime($values[$code]['from']));
                                        }
                                        $filter[$table]->where('value >= ?', $values[$code]['from']);
                                    }


                                    if ($values[$code]['to']) {
                                        if (!is_numeric($values[$code]['to'])) {
                                            $values[$code]['to'] = date("Y-m-d H:i:s", strtotime($values[$code]['to']));
                                        }
                                        $filter[$table]->where('value <= ?', $values[$code]['to']);
                                    }
                                } else {
                                    $filter[$table]->where('value in (?)', $values[$code]);
                                }
                            } else {
                                $filter[$table]->where('value = ?', $values[$code]);
                            }
                            $filter[$table]->where('store_id = ?)', $store);
                            $filteredAttributes[]=$code;
                        }
                    }
                }
            }
        }

        return $filter;
    }

    protected function _getSelect()
    {
        return $this->_getResource()->getReadConnection()->select();
    }

    public function index($product)
    {
        $stores = $this->_getStores();
        foreach ($stores as $store) {
            $product = Mage::getModel('catalog/product')
                ->setStoreId($store->getId())
                ->load($product->getId());

            $this->_runIndexingProcess($product);
        }
    }


    protected function _runIndexingProcess(Mage_Catalog_Model_Product $product)
    {
        foreach ($this->_indexers as $indexer) {
            $indexer->processAfterSave($product);
        }
    }
/*
    public function reindex()
    {
        $products = array();
        foreach ($this->_getStores() as $store) {
            $productsByStore = $this->_getResource()->getProducts($store->getId());
            foreach ($productsByStore as $product) {
                $this->_runIndexingProcess($product);
            }
        }

    }
*/
    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
    }

    public function reindex()
    {
        $status = Mage_Catalog_Model_Product_Status::STATUS_ENABLED;
        $visibility = array(
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_BOTH,
            Mage_Catalog_Model_Product_Visibility::VISIBILITY_IN_CATALOG
        );

        $emptyCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter('status', $status)
            ->addAttributeToFilter('visibility', $visibility);

        $this->_addFilterableAttributesToCollection($emptyCollection);

        foreach ($this->_getStores() as $store) {
            $collection = clone $emptyCollection;
            $collection->setStore($store)->load();
            foreach ($collection as $product) {
                $this->_runIndexingProcess($product->setStoreId($store->getId()));
            }
        }
    }

}