<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */

 /**
 * Enterprise search model observer
 *
 * @category   Magento
 * @package    Magento_Search
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Magento_Search_Model_Observer
{
    /**
     * Add search weight field to attribute edit form (only for quick search)
     * @see Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     *
     * @param \Magento\Event\Observer $observer
     */
    public function eavAttributeEditFormInit(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $form      = $observer->getEvent()->getForm();
        $attribute = $observer->getEvent()->getAttribute();
        $fieldset  = $form->getElement('front_fieldset');

        $fieldset->addField('search_weight', 'select', array(
            'name'        => 'search_weight',
            'label'       => __('Search Weight'),
            'values'      => Mage::getModel('Magento_Search_Model_Source_Weight')->getOptions(),
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
     * @param \Magento\Event\Observer $observer
     */
    public function searchQueryEditFormAfterSave(\Magento\Event\Observer $observer)
    {
        $searchQuryModel = $observer->getEvent()->getDataObject();
        $queryId         = $searchQuryModel->getId();
        $relatedQueries  = $searchQuryModel->getSelectedQueriesGrid();

        if (strlen($relatedQueries) == 0) {
            $relatedQueries = array();
        } else {
            $relatedQueries = explode('&', $relatedQueries);
        }

        Mage::getResourceModel('Magento_Search_Model_Resource_Recommendations')
            ->saveRelatedQueries($queryId, $relatedQueries);
    }

    /**
     * Invalidate catalog search index after creating of new customer group or changing tax class of existing,
     * because there are all combinations of customer groups and websites per price stored at search engine index
     * and there will be no document's price field for customers that belong to new group or data will be not actual.
     *
     * @param \Magento\Event\Observer $observer
     */
    public function customerGroupSaveAfter(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object->isObjectNew() || $object->getTaxClassId() != $object->getOrigData('tax_class_id')) {
            Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }

    /**
     * Hold commit at indexation start if needed
     *
     * @param \Magento\Event\Observer $observer
     */
    public function holdCommit(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $engine = Mage::helper('Magento_CatalogSearch_Helper_Data')->getEngine();
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
     * @param \Magento\Event\Observer $observer
     */
    public function applyIndexChanges(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $engine = Mage::helper('Magento_CatalogSearch_Helper_Data')->getEngine();
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
        $cacheTag = Mage::getSingleton('Magento_Search_Model_Catalog_Layer_Filter_Price')->getCacheTag();
        Mage::app()->cleanCache(array($cacheTag));
    }

    /**
     * Store searchable attributes at adapter to avoid new collection load there
     *
     * @param \Magento\Event\Observer $observer
     */
    public function storeSearchableAttributes(\Magento\Event\Observer $observer)
    {
        $engine     = $observer->getEvent()->getEngine();
        $attributes = $observer->getEvent()->getAttributes();
        if (!$engine || !$attributes || !Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute->usesSource()) {
                continue;
            }

            $optionCollection = Mage::getResourceModel('Magento_Eav_Model_Resource_Entity_Attribute_Option_Collection')
                ->setAttributeFilter($attribute->getAttributeId())
                ->setPositionOrder(\Magento\DB\Select::SQL_ASC, true)
                ->load();

            $optionsOrder = array();
            foreach ($optionCollection as $option) {
                $optionsOrder[] = $option->getOptionId();
            }
            $optionsOrder = array_flip($optionsOrder);

            $attribute->setOptionsOrder($optionsOrder);
        }

        $engine->storeSearchableAttributes($attributes);
    }

    /**
     * Save store ids for website or store group before deleting
     * because lazy load for this property is used and this info is unavailable after deletion
     *
     * @param \Magento\Event\Observer $observer
     */
    public function saveStoreIdsBeforeScopeDelete(\Magento\Event\Observer $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $object->getStoreIds();
    }

    /**
     * Clear index data for deleted stores
     *
     * @param \Magento\Event\Observer $observer
     */
    public function clearIndexForStores(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object instanceof Magento_Core_Model_Website
            || $object instanceof Magento_Core_Model_Store_Group
        ) {
            $storeIds = $object->getStoreIds();
        } elseif ($object instanceof Magento_Core_Model_Store) {
            $storeIds = $object->getId();
        } else {
            $storeIds = array();
        }

        if (!empty($storeIds)) {
            $engine = Mage::helper('Magento_CatalogSearch_Helper_Data')->getEngine();
            $engine->cleanIndex($storeIds);
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param \Magento\Event\Observer $observer
     */
    public function resetCurrentCatalogLayer(\Magento\Event\Observer $observer)
    {
        if (Mage::helper('Magento_Search_Helper_Data')->getIsEngineAvailableForNavigation()) {
            Mage::register('current_layer', Mage::getSingleton('Magento_Search_Model_Catalog_Layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param \Magento\Event\Observer $observer
     */
    public function resetCurrentSearchLayer(\Magento\Event\Observer $observer)
    {
        if (Mage::helper('Magento_Search_Helper_Data')->getIsEngineAvailableForNavigation(false)) {
            Mage::register('current_layer', Mage::getSingleton('Magento_Search_Model_Search_Layer'));
        }
    }

    /**
     * Reindex data after price reindex
     *
     * @param \Magento\Event\Observer $observer
     */
    public function runFulltextReindexAfterPriceReindex(\Magento\Event\Observer $observer)
    {
        if (!Mage::helper('Magento_Search_Helper_Data')->isThirdPartyEngineAvailable()) {
            return;
        }

        /* @var Magento_Search_Model_Indexer_Indexer $indexer */
        $indexer = Mage::getSingleton('Magento_Index_Model_Indexer')->getProcessByCode('catalogsearch_fulltext');
        if (empty($indexer)) {
            return;
        }

        if ('process' == strtolower(Mage::app()->getRequest()->getControllerName())) {
            $indexer->reindexAll();
        } else {
            $indexer->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }
}
