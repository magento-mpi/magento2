<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @copyright   {copyright}
 * @license     {license_link}
 */
class Magento_Catalog_Model_Product_Indexer_Flat extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_product_flat_match_result';

    /**
     * Index math Entities array
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_MASS_ACTION,
        ),
        Magento_Catalog_Model_Resource_Eav_Attribute::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_DELETE,
        ),
        Magento_Core_Model_Store::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_DELETE
        ),
        Magento_Core_Model_Store_Group::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Catalog_Model_Product_Flat_Indexer::ENTITY => array(
            Magento_Catalog_Model_Product_Flat_Indexer::EVENT_TYPE_REBUILD,
        ),
    );

    /**
     * Whether the indexer should be displayed on process/list page
     *
     * @return bool
     */
    public function isVisible()
    {
        /** @var $productFlatHelper Magento_Catalog_Helper_Product_Flat */
        $productFlatHelper = Mage::helper('Magento_Catalog_Helper_Product_Flat');
        return $productFlatHelper->isEnabled() || !$productFlatHelper->isBuilt();
    }

    /**
     * Retrieve Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Product Flat Data');
    }

    /**
     * Retrieve Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Reorganize EAV product structure to flat structure');
    }

    /**
     * Retrieve Catalog Product Flat Indexer model
     *
     * @return Magento_Catalog_Model_Product_Flat_Indexer
     */
    protected function _getIndexer()
    {
        return Mage::getSingleton('Magento_Catalog_Model_Product_Flat_Indexer');
    }

    /**
     * Check if event can be matched by process
     * Overwrote for check is flat catalog product is enabled and specific save
     * attribute, store, store_group
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event)
    {
        /** @var $productFlatHelper Magento_Catalog_Helper_Product_Flat */
        $productFlatHelper = $event->getFlatHelper() ?: Mage::helper('Magento_Catalog_Helper_Product_Flat');
        if (!$productFlatHelper->isAvailable() || !$productFlatHelper->isBuilt()) {
            return false;
        }

        $data = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        switch ($entity) {
            case Magento_Catalog_Model_Resource_Eav_Attribute::ENTITY:
                $result = $this->_matchAttributeEvent($event, $productFlatHelper);
                break;

            case Magento_Core_Model_Store::ENTITY:
                $result = $this->_matchStoreEvent($event);
                break;

            case Magento_Core_Model_Store_Group::ENTITY:
                $result = $this->_matchStoreGroupEvent($event);
                break;

            default:
                $result = parent::matchEvent($event);
                break;
        }

        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, $result);

        return $result;
    }

    /**
     * Whether a store group available for matching or not
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    protected function _matchStoreGroupEvent(Magento_Index_Model_Event $event)
     {
         /* @var $storeGroup Magento_Core_Model_Store_Group */
         $storeGroup = $event->getDataObject();
         if ($storeGroup && $storeGroup->dataHasChangedFor('website_id')) {
             return true;
         }
         return false;
     }

    /**
     * Whether a store available for matching or not
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    protected function _matchStoreEvent(Magento_Index_Model_Event $event)
    {
        if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
            return true;
        } else {
            /* @var $store Magento_Core_Model_Store */
            $store = $event->getDataObject();
            if ($store && $store->isObjectNew()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Whether an attribute available for matching or not
     *
     * @param Magento_Index_Model_Event $event
     * @param $productFlatHelper
     * @return bool
     */
    protected function _matchAttributeEvent(Magento_Index_Model_Event $event, $productFlatHelper)
    {
        $attribute = $event->getDataObject();
        if (!$attribute) {
            return false;
        }

        $enableBefore = $this->_isAttributeEnabled($attribute, $productFlatHelper);
        $enableAfter = $this->_isAttributeEnabled($attribute, $productFlatHelper, false);

        if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
            return $enableBefore;
        } elseif ($event->getType() == Magento_Index_Model_Event::TYPE_SAVE && ($enableAfter || $enableBefore)) {
            return true;
        }

        return false;
    }

    /**
     * Whether an attribute available for matching or not
     *
     * @param Magento_Catalog_Model_Resource_Eav_Attribute $attribute
     * @param Magento_Catalog_Helper_Product_Flat $productFlatHelper
     * @param bool $before
     * @return bool
     */
    protected function _isAttributeEnabled($attribute, $productFlatHelper, $before = true) {

        $method = $before ? 'getOrigData': 'getData';

        return $attribute && (($attribute->$method('backend_type') == 'static')
            || ($productFlatHelper->isAddFilterableAttributes() && $attribute->$method('is_filterable') > 0)
            || ($attribute->$method('used_in_product_listing') == 1)
            || ($attribute->$method('is_used_for_promo_rules') == 1)
            || ($attribute->$method('used_for_sort_by') == 1));
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
            case Magento_Catalog_Model_Product::ENTITY:
                $this->_registerCatalogProductEvent($event);
                break;
            case Magento_Core_Model_Store::ENTITY:
                if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
                    $this->_registerCoreStoreEvent($event);
                    break;
                }
            case Magento_Catalog_Model_Resource_Eav_Attribute::ENTITY:
            case Magento_Core_Model_Store_Group::ENTITY:
                $event->addNewData('catalog_product_flat_skip_call_event_handler', true);
                $process = $event->getProcess();
                $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
            case Magento_Catalog_Model_Product_Flat_Indexer::ENTITY:
                switch ($event->getType()) {
                    case Magento_Catalog_Model_Product_Flat_Indexer::EVENT_TYPE_REBUILD:
                        $event->addNewData('id', $event->getDataObject()->getId());
                }
                break;
        }
    }

    /**
     * Register data required by catalog product process in event object
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Flat
     */
    protected function _registerCatalogProductEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case Magento_Index_Model_Event::TYPE_SAVE:
                /* @var $product Magento_Catalog_Model_Product */
                $product = $event->getDataObject();
                $event->addNewData('catalog_product_flat_product_id', $product->getId());
                break;

            case Magento_Index_Model_Event::TYPE_MASS_ACTION:
                /* @var $actionObject \Magento\Object */
                $actionObject = $event->getDataObject();

                $reindexData  = array();
                $reindexFlat  = false;

                // check if status changed
                $attrData = $actionObject->getAttributesData();
                if (isset($attrData['status'])) {
                    $reindexFlat = true;
                    $reindexData['catalog_product_flat_status'] = $attrData['status'];
                }

                // check changed websites
                if ($actionObject->getWebsiteIds()) {
                    $reindexFlat = true;
                    $reindexData['catalog_product_flat_website_ids'] = $actionObject->getWebsiteIds();
                    $reindexData['catalog_product_flat_action_type'] = $actionObject->getActionType();
                }

                $flatAttributes = array();
                if (is_array($attrData)) {
                    $flatAttributes = array_intersect($this->_getFlatAttributes(), array_keys($attrData));
                }

                if (count($flatAttributes) > 0) {
                    $reindexFlat = true;
                    $reindexData['catalog_product_flat_force_update'] = true;
                }

                // register affected products
                if ($reindexFlat) {
                    $reindexData['catalog_product_flat_product_ids'] = $actionObject->getProductIds();
                    foreach ($reindexData as $k => $v) {
                        $event->addNewData($k, $v);
                    }
                }
                break;
        }

        return $this;
    }

    /**
     * Register core store delete process
     *
     * @param Magento_Index_Model_Event $event
     * @return Magento_Catalog_Model_Product_Indexer_Flat
     */
    protected function _registerCoreStoreEvent(Magento_Index_Model_Event $event)
    {
        if ($event->getType() == Magento_Index_Model_Event::TYPE_DELETE) {
            /* @var $store Magento_Core_Model_Store */
            $store = $event->getDataObject();
            $event->addNewData('catalog_product_flat_delete_store_id', $store->getId());
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
        if ($event->getType() == Magento_Catalog_Model_Product_Flat_Indexer::EVENT_TYPE_REBUILD) {
            $this->_getIndexer()->getResource()->rebuild($data['id']);
            return;
        }


        if (!empty($data['catalog_product_flat_reindex_all'])) {
            $this->reindexAll();
        } else if (!empty($data['catalog_product_flat_product_id'])) {
            // catalog_product save
            $productId = $data['catalog_product_flat_product_id'];
            $this->_getIndexer()->saveProduct($productId);
        } else if (!empty($data['catalog_product_flat_product_ids'])) {
            // catalog_product mass_action
            $productIds = $data['catalog_product_flat_product_ids'];

            if (!empty($data['catalog_product_flat_website_ids'])) {
                $websiteIds = $data['catalog_product_flat_website_ids'];
                foreach ($websiteIds as $websiteId) {
                    $website = Mage::app()->getWebsite($websiteId);
                    foreach ($website->getStores() as $store) {
                        if ($data['catalog_product_flat_action_type'] == 'remove') {
                            $this->_getIndexer()->removeProduct($productIds, $store->getId());
                        } else {
                            $this->_getIndexer()->updateProduct($productIds, $store->getId());
                        }
                    }
                }
            }

            if (isset($data['catalog_product_flat_status'])) {
                $status = $data['catalog_product_flat_status'];
                $this->_getIndexer()->updateProductStatus($productIds, $status);
            }

            if (isset($data['catalog_product_flat_force_update'])) {
                $this->_getIndexer()->updateProduct($productIds);
            }
        } else if (!empty($data['catalog_product_flat_delete_store_id'])) {
            $this->_getIndexer()->deleteStore($data['catalog_product_flat_delete_store_id']);
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

    /**
     * Retrieve list of attribute codes, that are used in flat
     *
     * @return array
     */
    protected function _getFlatAttributes()
    {
        return Mage::getModel('Magento_Catalog_Model_Product_Flat_Indexer')->getAttributeCodes();
    }
}
