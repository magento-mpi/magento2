<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Catalog Category Flat Indexer Model
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Indexer_Flat extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_category_flat_match_result';

    /**
     * Matched entity events
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Category::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Core_Model_Store::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_DELETE
        ),
        Magento_Core_Model_Store_Group::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    /**
     * Catalog category flat
     *
     * @var Magento_Catalog_Helper_Category_Flat
     */
    protected $_catalogCategoryFlat = null;

    /**
     * @param Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Catalog_Helper_Category_Flat $catalogCategoryFlat,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_catalogCategoryFlat = $catalogCategoryFlat;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    public function isVisible()
    {
        return $this->_catalogCategoryFlat->isEnabled() || !$this->_catalogCategoryFlat->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Category Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Reorganize EAV category structure to flat structure');
    }

    /**
     * Retrieve Catalog Category Flat Indexer model
     *
     * @return Magento_Catalog_Model_Resource_Category_Flat
     */
    protected function _getIndexer()
    {
        return Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Category_Flat');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat catalog category is enabled and specific save
     * category, store, store_group
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event)
    {
        if (!$this->_catalogCategoryFlat->isAvailable() || !$this->_catalogCategoryFlat->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Magento_Core_Model_Store::ENTITY) {
            if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
                $result = true;
            } elseif ($event->getType() == Magento_Index_Model_Event::TYPE_SAVE) {
                /** @var $store Magento_Core_Model_Store */
                $store = $event->getDataObject();
                if ($store && ($store->isObjectNew()
                    || $store->dataHasChangedFor('group_id')
                    || $store->dataHasChangedFor('root_category_id')
                )) {
                    $result = true;
                } else {
                    $result = false;
                }
            } else {
                $result = false;
            }
        } elseif ($entity == Magento_Core_Model_Store_Group::ENTITY) {
            /** @var $storeGroup Magento_Core_Model_Store_Group */
            $storeGroup = $event->getDataObject();
            if ($storeGroup
                && ($storeGroup->dataHasChangedFor('website_id') || $storeGroup->dataHasChangedFor('root_category_id'))
            ) {
                $result = true;
            } else {
                $result = false;
            }
        } else {
            $result = parent::matchEvent($event);
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Register data required by process in event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        switch ($event->getEntity()) {
            case Magento_Catalog_Model_Category::ENTITY:
                $this->_registerCatalogCategoryEvent($event);
                break;

            case Magento_Core_Model_Store::ENTITY:
                if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
            case Magento_Core_Model_Store_Group::ENTITY:
                $event->addNewData('catalog_category_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
    }

    /**
     * Register data required by catalog category process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Category_Indexer_Flat
     */
    protected function _registerCatalogCategoryEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Magento_Index_Model_Event::TYPE_SAVE:
                /* @var $category Magento_Catalog_Model_Category */
                $category = $event->getDataObject();

                /**
                 * Check if category has another affected category ids (category move result)
                 */
                $affectedCategoryIds = $category->getAffectedCategoryIds();
                if ($affectedCategoryIds) {
                    $event->addNewData('catalog_category_flat_affected_category_ids', $affectedCategoryIds);
                } else {
                    $event->addNewData('catalog_category_flat_category_id', $category->getId());
                }

                break;
        }
        return $this;
    }

    /**
     * Register core store delete process
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Category_Indexer_Flat
     */
    protected function _registerCoreStoreEvent(Magento_Index_Model_Event $event)
    {
        if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
            /* @var $store Magento_Core_Model_Store */
            $store = $event->getDataObject();
            $event->addNewData('catalog_category_flat_delete_store_id', $store->getId());
        }
        return $this;
    }

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();

        if (!empty($data['catalog_category_flat_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['catalog_category_flat_category_id'])) {
            // catalog_product save
            $categoryId = $data['catalog_category_flat_category_id'];
            $this->_getIndexer()->synchronize($categoryId);
        } else if (!empty($data['catalog_category_flat_affected_category_ids'])) {
            $categoryIds = $data['catalog_category_flat_affected_category_ids'];
            $this->_getIndexer()->move($categoryIds);
        } else if (!empty($data['catalog_category_flat_delete_store_id'])) {
            $storeId = $data['catalog_category_flat_delete_store_id'];
            $this->_getIndexer()->deleteStores($storeId);
        }
    }

    /**
     * Rebuild all index data
     *
     */
    public function reindexAll()
    {
        $this->_getIndexer()->reindexAll();
    }
}
