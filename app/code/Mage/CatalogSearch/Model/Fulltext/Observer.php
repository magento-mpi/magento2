<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @category    Mage
 * @package     Mage_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_CatalogSearch_Model_Fulltext_Observer
{
    /**
     * Retrieve fulltext (indexer) model
     *
     * @return Mage_CatalogSearch_Model_Fulltext
     */
    protected function _getFulltextModel()
    {
        return Mage::getSingleton('Mage_CatalogSearch_Model_Fulltext');
    }

    /**
     * Update product index when product data updated
     *
     * @deprecated since 1.11
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshProductIndex(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->rebuildIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Clean product index when product deleted or marked as unsearchable/invisible
     *
     * @deprecated since 1.11
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function cleanProductIndex(Varien_Event_Observer $observer)
    {
        $product = $observer->getEvent()->getProduct();

        $this->_getFulltextModel()
            ->cleanIndex(null, $product->getId())
            ->resetSearchResults();

        return $this;
    }

    /**
     * Update all attribute-dependant index
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function eavAttributeChange(Varien_Event_Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Mage_Eav_Model_Entity_Attribute */
        $entityType = Mage::getSingleton('Mage_Eav_Model_Config')->getEntityType(Mage_Catalog_Model_Product::ENTITY);
        /* @var $entityType Mage_Eav_Model_Entity_Type */

        if ($attribute->getEntityTypeId() != $entityType->getId()) {
            return $this;
        }
        $delete = $observer->getEventName() == 'eav_entity_attribute_delete_after';

        if (!$delete && !$attribute->dataHasChangedFor('is_searchable')) {
            return $this;
        }

        $showNotice = false;
        if ($delete) {
            if ($attribute->getIsSearchable()) {
                $showNotice = true;
            }
        }
        elseif ($attribute->dataHasChangedFor('is_searchable')) {
            $showNotice = true;
        }

        if ($showNotice) {
            $url = Mage::getSingleton('Mage_Backend_Model_Url')->getUrl('adminhtml/system_cache');
            Mage::getSingleton('Mage_Adminhtml_Model_Session')->addNotice(
                Mage::helper('Mage_CatalogSearch_Helper_Data')->__('Attribute setting change related with Search Index. Please run <a href="%1">Rebuild Search Index</a> process.', $url)
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshIndexAfterImport()
    {
        $this->_getFulltextModel()
            ->rebuildIndex();
        return $this;
    }

    /**
     * Refresh fulltext index when we add new store
     *
     * @param   Varien_Event_Observer $observer
     * @return  Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshStoreIndex(Varien_Event_Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_getFulltextModel()->rebuildIndex($storeId);
        return $this;
    }

    /**
     * Catalog Product mass website update
     *
     * @deprecated since 1.11
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function catalogProductWebsiteUpdate(Varien_Event_Observer $observer)
    {
        $websiteIds = $observer->getEvent()->getWebsiteIds();
        $productIds = $observer->getEvent()->getProductIds();
        $actionType = $observer->getEvent()->getAction();

        foreach ($websiteIds as $websiteId) {
            foreach (Mage::app()->getWebsite($websiteId)->getStoreIds() as $storeId) {
                if ($actionType == 'remove') {
                    $this->_getFulltextModel()
                        ->cleanIndex($storeId, $productIds)
                        ->resetSearchResults();
                }
                elseif ($actionType == 'add') {
                    $this->_getFulltextModel()
                        ->rebuildIndex($storeId, $productIds)
                        ->resetSearchResults();
                }
            }
        }

        return $this;
    }

    /**
     * Store delete processing
     *
     * @param Varien_Event_Observer $observer
     * @return Mage_CatalogSearch_Model_Fulltext_Observer
     */
    public function cleanStoreIndex(Varien_Event_Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Mage_Core_Model_Store */

        $this->_getFulltextModel()
            ->cleanIndex($store->getId());

        return $this;
    }
}
