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
 * Event observer and indexer running application
 *
 * @author Sasha Boyko <alex.boyko@varien.com>
 */
class Mage_CatalogIndex_Model_Observer extends Mage_Core_Model_Abstract
{
    protected $_indexers = array();

    protected function _construct()
    {
        $this->_loadIndexers();
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

    public function processAfterSaveEvent(Varien_Event_Observer $observer)
    {
        $this->_runIndexingProcess($observer->getEvent()->getProduct());
    }

    protected function _runIndexingProcess(Mage_Catalog_Model_Product $product)
    {
        foreach ($this->_indexers as $indexer) {
            $indexer->processAfterSave($product);
        }
    }

    protected function _addFilterableAttributesToCollection($collection)
    {
        $attributeCodes = $this->_getIndexableAttributeCodes();
        foreach ($attributeCodes as $code) {
            $collection->addAttributeToSelect($code);
        }

        return $this;
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

    protected function _getStores()
    {
        $stores = $this->getData('_stores');
        if (is_null($stores)) {
            $stores = array();

            $stores = Mage::getModel('core/store')->getCollection()->load();
/*
            $websites = Mage::app()->getWebsites();
            if (is_array($websites)) {
                foreach ($websites as $website) {
                    if (is_array($website->getStores())) {
                        $stores = array_merge($stores, $website->getStores());
                    }
                }
            }
*/
            
            $this->setData('_stores', $stores);
        }
        return $stores;
    }

    public function reindexAll()
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