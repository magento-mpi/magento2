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
     * Source weight
     *
     * @var \Magento\Search\Model\Source\Weight
     */
    protected $_sourceWeight;

    /**
     * @param CollectionFactory $eavEntityAttributeOptionCollectionFactory
     * @param Resource\RecommendationsFactory $searchRecommendationsFactory
     * @param \Magento\Indexer\Model\IndexerFactory $indexerFactory
     * @param \Magento\Search\Helper\Data $searchData
     * @param Source\Weight $sourceWeight
     */
    public function __construct(
        CollectionFactory $eavEntityAttributeOptionCollectionFactory,
        \Magento\Search\Model\Resource\RecommendationsFactory $searchRecommendationsFactory,
        \Magento\Indexer\Model\IndexerFactory $indexerFactory,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Search\Model\Source\Weight $sourceWeight
    ) {
        $this->_eavEntityAttributeOptionCollectionFactory = $eavEntityAttributeOptionCollectionFactory;
        $this->_searchRecommendationsFactory = $searchRecommendationsFactory;
        $this->_searchData = $searchData;
        $this->_sourceWeight = $sourceWeight;
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
     * Store searchable attributes at adapter to avoid new collection load there
     *
     * @param EventObserver $observer
     * @return void
     */
    public function storeSearchableAttributes(EventObserver $observer)
    {
        /** @var \Magento\CatalogSearch\Model\Resource\EngineInterface $engine */
        $engine = $observer->getEvent()->getEngine();
        /** @var \Magento\Eav\Model\Entity\Attribute[] $attributes */
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
                /** @var \Magento\Eav\Model\Entity\Attribute\Option $option */
                $optionsOrder[] = $option->getOptionId();
            }
            $optionsOrder = array_flip($optionsOrder);

            $attribute->setOptionsOrder($optionsOrder);
        }

        $engine->storeSearchableAttributes($attributes);
    }
}
