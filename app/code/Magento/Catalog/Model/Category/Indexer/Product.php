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
 * Category products indexer model.
 * Responsibility for system actions:
 *  - Product save (changed assigned categories list)
 *  - Category save (changed assigned products list or category move)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *
 * @method Magento_Catalog_Model_Resource_Category_Indexer_Product _getResource()
 * @method Magento_Catalog_Model_Resource_Category_Indexer_Product getResource()
 * @method int getCategoryId()
 * @method Magento_Catalog_Model_Category_Indexer_Product setCategoryId(int $value)
 * @method int getProductId()
 * @method Magento_Catalog_Model_Category_Indexer_Product setProductId(int $value)
 * @method int getPosition()
 * @method Magento_Catalog_Model_Category_Indexer_Product setPosition(int $value)
 * @method int getIsParent()
 * @method Magento_Catalog_Model_Category_Indexer_Product setIsParent(int $value)
 * @method int getStoreId()
 * @method Magento_Catalog_Model_Category_Indexer_Product setStoreId(int $value)
 * @method int getVisibility()
 * @method Magento_Catalog_Model_Category_Indexer_Product setVisibility(int $value)
 *
 * @category    Magento
 * @package     Magento_Catalog
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Magento_Catalog_Model_Category_Indexer_Product extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_category_product_match_result';

    /**
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE,
            Magento_Index_Model_Event::TYPE_MASS_ACTION
        ),
        Magento_Catalog_Model_Category::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Core_Model_Store::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
        Magento_Core_Model_Store_Group::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
    );

    /**
     * Initialize resource
     */
    protected function _construct()
    {
        $this->_init('Magento_Catalog_Model_Resource_Category_Indexer_Product');
    }

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Category Products');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Indexed category/products association');
    }

    /**
     * Check if event can be matched by process.
     * Overwrote for specific config save, store and store groups save matching
     *
     * @param Magento_Index_Model_Event $event
     * @return bool
     */
    public function matchEvent(Magento_Index_Model_Event $event)
    {
        $data      = $event->getNewData();
        if (isset($data[self::EVENT_MATCH_RESULT_KEY])) {
            return $data[self::EVENT_MATCH_RESULT_KEY];
        }

        $entity = $event->getEntity();
        if ($entity == Magento_Core_Model_Store::ENTITY) {
            $store = $event->getDataObject();
            if ($store && ($store->isObjectNew() || $store->dataHasChangedFor('group_id'))) {
                $result = true;
            } else {
                $result = false;
            }
        } elseif ($entity == Magento_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
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
     * Check if category ids was changed
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        $event->addNewData(self::EVENT_MATCH_RESULT_KEY, true);
        $entity = $event->getEntity();
        switch ($entity) {
            case Magento_Catalog_Model_Product::ENTITY:
               $this->_registerProductEvent($event);
                break;

            case Magento_Catalog_Model_Category::ENTITY:
                $this->_registerCategoryEvent($event);
                break;

            case Magento_Core_Model_Store::ENTITY:
            case Magento_Core_Model_Store_Group::ENTITY:
                $process = $event->getProcess();
                $process->changeStatus(Magento_Index_Model_Process::STATUS_REQUIRE_REINDEX);
                break;
        }
        return $this;
    }

    /**
     * Register event data during product save process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerProductEvent(Magento_Index_Model_Event $event)
    {
        $eventType = $event->getType();
        if ($eventType == Magento_Index_Model_Event::TYPE_SAVE) {
            $product = $event->getDataObject();
            /**
             * Check if product categories data was changed
             */
            if ($product->getIsChangedCategories() || $product->dataHasChangedFor('status')
                || $product->dataHasChangedFor('visibility') || $product->getIsChangedWebsites()) {
                $event->addNewData('category_ids', $product->getCategoryIds());
            }
        } else if ($eventType == Magento_Index_Model_Event::TYPE_MASS_ACTION) {
            /* @var $actionObject \Magento\Object */
            $actionObject = $event->getDataObject();
            $attributes   = array('status', 'visibility');
            $rebuildIndex = false;

            // check if attributes changed
            $attrData = $actionObject->getAttributesData();
            if (is_array($attrData)) {
                foreach ($attributes as $attributeCode) {
                    if (array_key_exists($attributeCode, $attrData)) {
                        $rebuildIndex = true;
                        break;
                    }
                }
            }

            // check changed websites
            if ($actionObject->getWebsiteIds()) {
                $rebuildIndex = true;
            }

            // register affected products
            if ($rebuildIndex) {
                $event->addNewData('product_ids', $actionObject->getProductIds());
            }
        }
    }

    /**
     * Register event data during category save process
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerCategoryEvent(Magento_Index_Model_Event $event)
    {
        $category = $event->getDataObject();
        /**
         * Check if product categories data was changed
         */
        if ($category->getIsChangedProductList()) {
            $event->addNewData('products_was_changed', true);
        }
        /**
         * Check if category has another affected category ids (category move result)
         */
        if ($category->getAffectedCategoryIds()) {
            $event->addNewData('affected_category_ids', $category->getAffectedCategoryIds());
        }
    }

    /**
     * Process event data and save to index
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_category_product_reindex_all'])) {
            $this->reindexAll();
        }
        if (empty($data['catalog_category_product_skip_call_event_handler'])) {
            $this->callEventHandler($event);
        }
    }
}
