<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Search\Model;

use Magento\Eav\Model\Resource\Entity\Attribute\Option\CollectionFactory;
use Magento\Framework\Event\Observer as EventObserver;

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
     * Search recommendations factory
     *
     * @var \Magento\Search\Model\Resource\RecommendationsFactory
     */
    protected $_searchRecommendationsFactory = null;

    /**
     * Eav entity attribute option coll factory
     *
     * @var CollectionFactory
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
     * @var \Magento\Framework\Registry
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
     * @var \Magento\Framework\App\RequestInterface
     */
    protected $_request;

    /**
     * @param CollectionFactory $eavEntityAttributeOptionCollectionFactory
     * @param Resource\RecommendationsFactory $searchRecommendationsFactory
     * @param \Magento\Index\Model\Indexer $indexer
     * @param \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Framework\Registry $coreRegistry
     * @param Source\Weight $sourceWeight
     * @param \Magento\Framework\App\RequestInterface $request
     */
    public function __construct(
        CollectionFactory $eavEntityAttributeOptionCollectionFactory,
        \Magento\Search\Model\Resource\RecommendationsFactory $searchRecommendationsFactory,
        \Magento\Index\Model\Indexer $indexer,
        \Magento\CatalogSearch\Model\Resource\EngineProvider $engineProvider,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Search\Model\Source\Weight $sourceWeight,
        \Magento\Framework\App\RequestInterface $request
    ) {
        $this->_eavEntityAttributeOptionCollectionFactory = $eavEntityAttributeOptionCollectionFactory;
        $this->_searchRecommendationsFactory = $searchRecommendationsFactory;
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

        $form = $observer->getEvent()->getForm();
        $attribute = $observer->getEvent()->getAttribute();
        $fieldset = $form->getElement('front_fieldset');

        $fieldset->addField(
            'search_weight',
            'select',
            array(
                'name' => 'search_weight',
                'label' => __('Search Weight'),
                'values' => $this->_sourceWeight->getOptions()
            ),
            'is_searchable'
        );
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
        $queryId = $searchQuryModel->getId();
        $relatedQueries = $searchQuryModel->getSelectedQueriesGrid();

        if (strlen($relatedQueries) == 0) {
            $relatedQueries = array();
        } else {
            $relatedQueries = explode('&', $relatedQueries);
        }

        $this->_searchRecommendationsFactory->create()->saveRelatedQueries($queryId, $relatedQueries);
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
            $this->_indexer->getProcessByCode(
                'catalogsearch_fulltext'
            )->changeStatus(
                \Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX
            );
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
        $engine = $observer->getEvent()->getEngine();
        $attributes = $observer->getEvent()->getAttributes();
        if (!$engine || !$attributes || !$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute->usesSource()) {
                continue;
            }

            $optionCollection = $this->_eavEntityAttributeOptionCollectionFactory->create()->setAttributeFilter(
                $attribute->getAttributeId()
            )->setPositionOrder(
                \Magento\Framework\DB\Select::SQL_ASC,
                true
            )->load();

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
        if ($object instanceof \Magento\Store\Model\Website || $object instanceof \Magento\Store\Model\Group) {
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
