<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise search model observer
 *
 * @category   Enterprise
 * @package    Enterprise_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Search_Model_Observer
{
    /**
     * Add search weight field to attribute edit form (only for quick search)
     * @see Mage_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     *
     * @param Varien_Event_Observer $observer
     */
    public function eavAttributeEditFormInit(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $form      = $observer->getEvent()->getForm();
        $attribute = $observer->getEvent()->getAttribute();
        $fieldset  = $form->getElement('front_fieldset');

        $fieldset->addField('search_weight', 'select', array(
            'name'        => 'search_weight',
            'label'       => Mage::helper('Mage_Catalog_Helper_Data')->__('Search Weight'),
            'values'      => Mage::getModel('Enterprise_Search_Model_Source_Weight')->getOptions(),
        ), 'is_searchable');
        /**
         * Disable default search fields
         */
        $attributeCode = $attribute->getAttributeCode();

        if ($attributeCode == 'name') {
            $form->getElement('is_searchable')->setDisabled(1);
        }
    }

    /**
     * Save search query relations after save search query
     *
     * @param Varien_Event_Observer $observer
     */
    public function searchQueryEditFormAfterSave(Varien_Event_Observer $observer)
    {
        $searchQuryModel = $observer->getEvent()->getDataObject();
        $queryId         = $searchQuryModel->getId();
        $relatedQueries  = $searchQuryModel->getSelectedQueriesGrid();

        if (strlen($relatedQueries) == 0) {
            $relatedQueries = array();
        } else {
            $relatedQueries = explode('&', $relatedQueries);
        }

        Mage::getResourceModel('Enterprise_Search_Model_Resource_Recommendations')
            ->saveRelatedQueries($queryId, $relatedQueries);
    }

    /**
     * Invalidate catalog search index after creating of new customer group or changing tax class of existing,
     * because there are all combinations of customer groups and websites per price stored at search engine index
     * and there will be no document's price field for customers that belong to new group or data will be not actual.
     *
     * @param Varien_Event_Observer $observer
     */
    public function customerGroupSaveAfter(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object->isObjectNew() || $object->getTaxClassId() != $object->getOrigData('tax_class_id')) {
            Mage::getSingleton('Mage_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Mage_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param Varien_Event_Observer $observer
     */
    public function holdCommit(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $engine = Mage::helper('Mage_CatalogSearch_Helper_Data')->getEngine();
        if (!$engine->holdCommit()) {
            return;
        }

        /*
         * Index needs to be optimized if all products were affected
         */
        $productIds = $observer->getEvent()->getProductIds();
        if (is_null($productIds)) {
            $engine->setIndexNeedsOptimization();
        }
    }

    /**
     * Apply changes in search engine index.
     * Make index optimization if documents were added to index.
     * Allow commit if it was held.
     *
     * @param Varien_Event_Observer $observer
     */
    public function applyIndexChanges(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $engine = Mage::helper('Mage_CatalogSearch_Helper_Data')->getEngine();
        if (!$engine->allowCommit()) {
            return;
        }

        if ($engine->getIndexNeedsOptimization()) {
            $engine->optimizeIndex();
        } else {
            $engine->commitChanges();
        }

        /**
         * Cleaning MAXPRICE cache
         */
        $cacheTag = Mage::getSingleton('Enterprise_Search_Model_Catalog_Layer_Filter_Price')->getCacheTag();
        Mage::app()->cleanCache(array($cacheTag));
    }

    /**
     * Save store ids for website or store group before deleting
     * because lazy load for this property is used and this info is unavailable after deletion
     *
     * @param Varien_Event_Observer $observer
     */
    public function saveStoreIdsBeforeScopeDelete(Varien_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $object->getStoreIds();
    }

    /**
     * Clear index data for deleted stores
     *
     * @param Varien_Event_Observer $observer
     */
    public function clearIndexForStores(Varien_Event_Observer $observer)
    {
        if (!Mage::helper('Enterprise_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object instanceof Mage_Core_Model_Website
            || $object instanceof Mage_Core_Model_Store_Group
        ) {
            $storeIds = $object->getStoreIds();
        } elseif ($object instanceof Mage_Core_Model_Store) {
            $storeIds = $object->getId();
        } else {
            $storeIds = array();
        }

        if (!empty($storeIds)) {
            $engine = Mage::helper('Mage_CatalogSearch_Helper_Data')->getEngine();
            $engine->cleanIndex($storeIds);
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Varien_Event_Observer $observer)
    {
        if (Mage::helper('Enterprise_Search_Helper_Data')->getIsEngineAvailableForNavigation()) {
            Mage::register('current_layer', Mage::getSingleton('Enterprise_Search_Model_Catalog_Layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Varien_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Varien_Event_Observer $observer)
    {
        if (Mage::helper('Enterprise_Search_Helper_Data')->getIsEngineAvailableForNavigation(false)) {
            Mage::register('current_layer', Mage::getSingleton('Enterprise_Search_Model_Search_Layer'));
        }
    }
}
