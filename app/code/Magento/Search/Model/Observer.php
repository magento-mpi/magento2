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
namespace Magento\Search\Model;

class Observer
{
    /**
     * Search data
     *
     * @var \Magento\Search\Helper\Data
     */
    protected $_searchData = null;

    /**
     * Core registry
     *
     * @var \Magento\Core\Model\Registry
     */
    protected $_coreRegistry = null;

    /**
     * Catalog search data
     *
     * @var \Magento\CatalogSearch\Helper\Data
     */
    protected $_catalogSearchData = null;

    /**
     * @param \Magento\CatalogSearch\Helper\Data $catalogSearchData
     * @param \Magento\Search\Helper\Data $searchData
     * @param \Magento\Core\Model\Registry $coreRegistry
     */
    public function __construct(
        \Magento\CatalogSearch\Helper\Data $catalogSearchData,
        \Magento\Search\Helper\Data $searchData,
        \Magento\Core\Model\Registry $coreRegistry
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_catalogSearchData = $catalogSearchData;
        $this->_searchData = $searchData;
    }

    /**
     * Add search weight field to attribute edit form (only for quick search)
     * @see \Magento\Adminhtml\Block\Catalog\Product\Attribute\Edit\Tab\Main
     *
     * @param \Magento\Event\Observer $observer
     */
    public function eavAttributeEditFormInit(\Magento\Event\Observer $observer)
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
            'values'      => \Mage::getModel('Magento\Search\Model\Source\Weight')->getOptions(),
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

        \Mage::getResourceModel('Magento\Search\Model\Resource\Recommendations')
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
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object->isObjectNew() || $object->getTaxClassId() != $object->getOrigData('tax_class_id')) {
            \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalogsearch_fulltext')
                ->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
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
        if (!$engine || !$attributes || !$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        foreach ($attributes as $attribute) {
            if (!$attribute->usesSource()) {
                continue;
            }

            $optionCollection = \Mage::getResourceModel('Magento\Eav\Model\Resource\Entity\Attribute\Option\Collection')
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
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        $object = $observer->getEvent()->getDataObject();
        if ($object instanceof \Magento\Core\Model\Website
            || $object instanceof \Magento\Core\Model\Store\Group
        ) {
            $storeIds = $object->getStoreIds();
        } elseif ($object instanceof \Magento\Core\Model\Store) {
            $storeIds = $object->getId();
        } else {
            $storeIds = array();
        }

        if (!empty($storeIds)) {
            $engine = $this->_catalogSearchData->getEngine();
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
        if ($this->_searchData->getIsEngineAvailableForNavigation()) {
            $this->_coreRegistry->register('current_layer', \Mage::getSingleton('Magento\Search\Model\Catalog\Layer'));
        }
    }

    /**
     * Reset search engine if it is enabled for search navigation
     *
     * @param \Magento\Event\Observer $observer
     */
    public function resetCurrentSearchLayer(\Magento\Event\Observer $observer)
    {
        if ($this->_searchData->getIsEngineAvailableForNavigation(false)) {
            $this->_coreRegistry->register('current_layer', \Mage::getSingleton('Magento\Search\Model\Search\Layer'));
        }
    }

    /**
     * Reindex data after price reindex
     *
     * @param \Magento\Event\Observer $observer
     */
    public function runFulltextReindexAfterPriceReindex(\Magento\Event\Observer $observer)
    {
        if (!$this->_searchData->isThirdPartyEngineAvailable()) {
            return;
        }

        /* @var \Magento\Search\Model\Indexer\Indexer $indexer */
        $indexer = \Mage::getSingleton('Magento\Index\Model\Indexer')->getProcessByCode('catalogsearch_fulltext');
        if (empty($indexer)) {
            return;
        }

        if ('process' == strtolower(\Mage::app()->getRequest()->getControllerName())) {
            $indexer->reindexAll();
        } else {
            $indexer->changeStatus(\Magento\Index\Model\Process::STATUS_REQUIRE_REINDEX);
        }
    }
}
