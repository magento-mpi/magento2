<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Search
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

use Magento\Event\Observer as EventObserver;
/**
 * Enterprise search model observer
 */
class Observer
{
    /**
     * Index indexer
     *
     * @var \Magento\Index\Model\Indexer
     */
    protected $_indexer;

    /**
     * Search catalog layer
     *
     * @var \Magento\Search\Model\Catalog\Layer
     */
    protected $_searchCatalogLayer = null;

    /**
     * Search search layer
     *
     * @var \Magento\Search\Model\Search\Layer
     */
    protected $_searchSearchLayer = null;

    /**
     * Search recommendations factory
     *
     * @var \Magento\Search\Model\Resource\RecommendationsFactory
     */
    protected $_searchRecommendationsFactory = null;

    /**
     * Eav entity attribute option coll factory
     *
     * @var \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory
     */
    protected $_eavEntityAttributeOptionCollectionFactory = null;

    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * Core registry
     *
     * @var \Magento\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Engine provider
     *
     * @var \Magento\CatalogSearch\Model\Resource\EngineProvider
     */
    protected $_engineProvider = null;

    /**
     * Source weight
     *
     * @var \Magento\Search\Model\Source\Weight
     */
    protected $_sourceWeight;

    /**
     * Request
     *
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * Construct
     *
     * @param \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $eavEntityAttributeOptionCollectionFactory
     * @param \Magento\Search\Model\Resource\RecommendationsFactory $searchRecommendationsFactory
     * @param \Magento\Search\Model\Search\Layer $searchSearchLayer
     * @param \Magento\Search\Model\Catalog\Layer $searchCatalogLayer
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Registry $coreRegistry
     * @param \Magento\Search\Model\Source\Weight $sourceWeight
     * @param \Magento\App\RequestInterface $request
     */
    public function __construct(
        \Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory $eavEntityAttributeOptionCollectionFactory,
        \Magento\Search\Model\Resource\RecommendationsFactory $searchRecommendationsFactory,
        \Magento\Search\Model\Search\Layer $searchSearchLayer,
        \Magento\Search\Model\Catalog\Layer $searchCatalogLayer,
        \Magento\Index\Model\Indexer $indexer,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Registry $coreRegistry,
        \Magento\Search\Model\Source\Weight $sourceWeight,
        \Magento\App\RequestInterface $request
    ) {
        $this->_eavEntityAttributeOptionCollectionFactory = $eavEntityAttributeOptionCollectionFactory;
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
     *
     * @param EventObserver $observer
     * @return void
     * @see \Magento\Catalog\Block\Adminhtml\Product\Attribute\Edit\Tab\Main
     */
    public function eavAttributeEditFormInit(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return void
     */
    public function searchQueryEditFormAfterSave(EventObserver $observer)
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
     * @param EventObserver $observer
     * @return void
     */
    public function customerGroupSaveAfter(EventObserver $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object->isObjectNew() || $object->getTaxClassId() != $object->getOrigData('tax_class_id')) {
            $this->_indexer->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
    }

    /**
     * Store searchable attributes at adapter to avoid new collection load there
     *
     * @param EventObserver $observer
     * @return void
     */
    public function storeSearchableAttributes(EventObserver $observer)
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

            $optionCollection = $this->_eavEntityAttributeOptionCollectionFactory->create()
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
     * @param EventObserver $observer
     * @return void
     */
    public function saveStoreIdsBeforeScopeDelete(EventObserver $observer)
    {
        $object = $observer->getEvent()->getDataObject();
        $object->getStoreIds();
    }

    /**
     * Clear index data for deleted stores
     *
     * @param EventObserver $observer
     * @return void
     */
    public function clearIndexForStores(EventObserver $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object instanceof \Magento\Store\Model\Website
            || $object instanceof \Magento\Store\Model\Store
        ) {
            $storeIds = $object->getStoreIds();
        } elseif ($object instanceof \Magento\Store\Model\Store) {
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
     * @param EventObserver $observer
     * @return void
     */
    public function resetCurrentCatalogLayer(EventObserver $observer)
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            $this->_coreRegistry->register('current_layer', $this->_searchCatalogLayer);
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param EventObserver $observer
     * @return void
     */
    public function resetCurrentSearchLayer(EventObserver $observer)
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation(false)) {
            $this->_coreRegistry->register('current_layer', $this->_searchSearchLayer);
        }
    }

    /**
     * Reindex data after price reindex
     *
     * @param EventObserver $observer
     * @return void
     */
    public function runFulltextReindexAfterPriceReindex(EventObserver $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        /* @var \Magento\Search\Model\Indexer\Indexer $indexer */
        $indexer = $this->_indexer->getProcessByCode('catalogsearch_fulltext');
        if (empty($indexer)) {
            return;
        }

        if ('process' == strtolower($this->_request->getControllerName())) {
            $indexer->reindexAll();
        } else {
            $indexer->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
    }
}
