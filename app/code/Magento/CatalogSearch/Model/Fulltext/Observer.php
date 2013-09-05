<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * CatalogSearch Fulltext Observer
 *
 * @category    Magento
 * @package     Magento_CatalogSearch
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_CatalogSearch_Model_Fulltext_Observer
{
    /**
     * Retrieve fulltext (indexer) model
     *
     * @return Magento_CatalogSearch_Model_Fulltext
     */
    protected function _getFulltextModel()
    {
        return Mage::getSingleton('Magento_CatalogSearch_Model_Fulltext');
    }

    /**
     * Update product index when product data updated
     *
     * @deprecated since 1.11
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshProductIndex(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function cleanProductIndex(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function eavAttributeChange(\Magento\Event\Observer $observer)
    {
        $attribute = $observer->getEvent()->getAttribute();
        /* @var $attribute Magento_Eav_Model_Entity_Attribute */
        $entityType = Mage::getSingleton('Magento_Eav_Model_Config')->getEntityType(Magento_Catalog_Model_Product::ENTITY);
        /* @var $entityType Magento_Eav_Model_Entity_Type */

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
            $url = Mage::getSingleton('Magento_Backend_Model_Url')->getUrl('adminhtml/system_cache');
            Mage::getSingleton('Magento_Adminhtml_Model_Session')->addNotice(
                __('Attribute setting change related with Search Index. Please run <a href="%1">Rebuild Search Index</a> process.', $url)
            );
        }

        return $this;
    }

    /**
     * Rebuild index after import
     *
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
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
     * @param   \Magento\Event\Observer $observer
     * @return  Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function refreshStoreIndex(\Magento\Event\Observer $observer)
    {
        $storeId = $observer->getEvent()->getStore()->getId();
        $this->_getFulltextModel()->rebuildIndex($storeId);
        return $this;
    }

    /**
     * Catalog Product mass website update
     *
     * @deprecated since 1.11
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function catalogProductWebsiteUpdate(\Magento\Event\Observer $observer)
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
     * @param \Magento\Event\Observer $observer
     * @return Magento_CatalogSearch_Model_Fulltext_Observer
     */
    public function cleanStoreIndex(\Magento\Event\Observer $observer)
    {
        $store = $observer->getEvent()->getStore();
        /* @var $store Magento_Core_Model_Store */

        $this->_getFulltextModel()
            ->cleanIndex($store->getId());

        return $this;
    }
}
