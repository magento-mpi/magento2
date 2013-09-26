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
 */
class Magento_Search_Model_Observer
{
    /**
     * Index indexer
     *
     * @var Magento_Index_Model_Indexer
     */
    protected $_indexer;

    /**
     * Search catalog layer
     *
     * @var Magento_Search_Model_Catalog_Layer
     */
    protected $_searchCatalogLayer = null;

    /**
     * Search search layer
     *
     * @var Magento_Search_Model_Search_Layer
     */
    protected $_searchSearchLayer = null;

    /**
     * Search recommendations factory
     *
     * @var Magento_Search_Model_Resource_RecommendationsFactory
     */
    protected $_searchRecommendationsFactory = null;

    /**
     * Eav entity attribute option coll factory
     *
     * @var Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory
     */
    protected $_eavEntityAttributeOptionCollFactory = null;

    /**
     * Search data
     *
     * @var Magento_Search_Helper_Data
     */
    protected $_searchData = null;

    /**
     * Core registry
     *
     * @var Magento_Core_Model_Registry
     */
    protected $_coreRegistry = null;

    /**
     * @var Magento_CatalogSearch_Model_Resource_EngineProvider
     */
    protected $_engineProvider = null;

    /**
     * Source weight
     *
     * @var Magento_Search_Model_Source_Weight
     */
    protected $_sourceWeight;

    /**
     * Request
     *
     * @var Magento_Core_Controller_Request_Http
     */
    protected $_request;

    /**
     * Construct
     *
     * @param Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $eavEntityAttributeOptionCollFactory
     * @param Magento_Search_Model_Resource_RecommendationsFactory $searchRecommendationsFactory
     * @param Magento_Search_Model_Search_Layer $searchSearchLayer
     * @param Magento_Search_Model_Catalog_Layer $searchCatalogLayer
     * @param Magento_Index_Model_Indexer $indexer
     * @param Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider
     * @param Magento_Search_Helper_Data $searchData
     * @param Magento_Core_Model_Registry $coreRegistry
     * @param Magento_Search_Model_Source_Weight $sourceWeight
     * @param Magento_Core_Controller_Request_Http $request
     */
    public function __construct(
        Magento_Eav_Model_Resource_Entity_Attribute_Option_CollectionFactory $eavEntityAttributeOptionCollFactory,
        Magento_Search_Model_Resource_RecommendationsFactory $searchRecommendationsFactory,
        Magento_Search_Model_Search_Layer $searchSearchLayer,
        Magento_Search_Model_Catalog_Layer $searchCatalogLayer,
        Magento_Index_Model_Indexer $indexer,
        Magento_CatalogSearch_Model_Resource_EngineProvider $engineProvider,
        Magento_Search_Helper_Data $searchData,
        Magento_Core_Model_Registry $coreRegistry,
        Magento_Search_Model_Source_Weight $sourceWeight,
        Magento_Core_Controller_Request_Http $request
    ) {
        $this->_eavEntityAttributeOptionCollFactory = $eavEntityAttributeOptionCollFactory;
        $this->_searchRecommendationsFactory = $searchRecommendationsFactory;
        $this->_searchSearchLayer = $searchSearchLayer;
        $this->_searchCatalogLayer = $searchCatalogLayer;
        $this->_indexer = $indexer;
        $this->_engineProvider = $engineProvider;
        $this->_searchData = $searchData;
        $this->_coreRegistry = $coreRegistry;
        $this->_sourceWeight = $sourceWeight;
        $this->_request = $request;
    }

    /**
     * Add search weight field to attribute edit form (only for quick search)
     * @see Magento_Adminhtml_Block_Catalog_Product_Attribute_Edit_Tab_Main
     *
     * @param Magento_Event_Observer $observer
     */
    public function eavAttributeEditFormInit(Magento_Event_Observer $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $form      = $observer->getEvent()->getForm();
        $attribute = $observer->getEvent()->getAttribute();
        $fieldset  = $form->getElement('front_fieldset');

        $fieldset->addField('search_weight', 'select', array(
            'name'        => 'search_weight',
            'label'       => __('Search Weight'),
            'values'      => $this->_sourceWeight->getOptions(),
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
     * @param Magento_Event_Observer $observer
     */
    public function searchQueryEditFormAfterSave(Magento_Event_Observer $observer)
    {
        $searchQuryModel = $observer->getEvent()->getDataObject();
        $queryId         = $searchQuryModel->getId();
        $relatedQueries  = $searchQuryModel->getSelectedQueriesGrid();

        if (strlen($relatedQueries) == 0) {
            $relatedQueries = array();
        } else {
            $relatedQueries = explode('&', $relatedQueries);
        }

        $this->_searchRecommendationsFactory->create()
            ->saveRelatedQueries($queryId, $relatedQueries);
    }

    /**
     * Invalidate catalog search index after creating of new customer group or changing tax class of existing,
     * because there are all combinations of customer groups and websites per price stored at search engine index
     * and there will be no document's price field for customers that belong to new group or data will be not actual.
     *
     * @param Magento_Event_Observer $observer
     */
    public function customerGroupSaveAfter(Magento_Event_Observer $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object->isObjectNew() || $object->getTaxClassId() != $object->getOrigData('tax_class_id')) {
            $this->_indexer->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }

    /**
     * Store searchable attributes at adapter to avoid new collection load there
     *
     * @param Magento_Event_Observer $observer
     */
    public function storeSearchableAttributes(Magento_Event_Observer $observer)
    {
        $engine     = $observer->getEvent()->getEngine();
        $attributes = $observer->getEvent()->getAttributes();
        if (!$engine || !$attributes || !$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute->usesSource()) {
                continue;
            }

            $optionCollection = $this->_eavEntityAttributeOptionCollFactory->create()
                ->setAttributeFilter($attribute->getAttributeId())
                ->setPositionOrder(Magento_DB_Select::SQL_ASC, true)
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
     * @param Magento_Event_Observer $observer
     */
    public function saveStoreIdsBeforeScopeDelete(Magento_Event_Observer $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $object->getStoreIds();
    }

    /**
     * Clear index data for deleted stores
     *
     * @param Magento_Event_Observer $observer
     */
    public function clearIndexForStores(Magento_Event_Observer $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
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
            $this->_engineProvider->get()->cleanIndex($storeIds);
        }
    }

    /**
     * Reset search engine if it is enabled for catalog navigation
     *
     * @param Magento_Event_Observer $observer
     */
    public function resetCurrentCatalogLayer(Magento_Event_Observer $observer)
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            $this->_coreRegistry->register('current_layer', $this->_searchCatalogLayer);
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param Magento_Event_Observer $observer
     */
    public function resetCurrentSearchLayer(Magento_Event_Observer $observer)
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation(false)) {
            $this->_coreRegistry->register('current_layer', $this->_searchSearchLayer);
        }
    }

    /**
     * Reindex data after price reindex
     *
     * @param Magento_Event_Observer $observer
     */
    public function runFulltextReindexAfterPriceReindex(Magento_Event_Observer $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        /* @var Magento_Search_Model_Indexer_Indexer $indexer */
        $indexer = $this->_indexer->getProcessByCode('catalogsearch_fulltext');
        if (empty($indexer)) {
            return;
        }

        if ('process' == strtolower($this->_request->getControllerName())) {
            $indexer->reindexAll();
        } else {
            $indexer->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
        }
    }
}
