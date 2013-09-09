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
 * Catalog url rewrites index model.
 * Responsibility for system actions:
 *  - Product save (changed assigned categories list, assigned websites or url key)
 *  - Category save (changed assigned products list, category move, changed url key)
 *  - Store save (new store creation, changed store group) - require reindex all data
 *  - Store group save (changed root category or group website) - require reindex all data
 *  - Seo config settings change - require reindex all data
 */
class Magento_Catalog_Model_Indexer_Url extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Data key for matching result to be saved in
     */
    const EVENT_MATCH_RESULT_KEY = 'catalog_url_match_result';

    /**
     * Index math: product save, category save, store save
     * store group save, config save
     *
     * @var array
     */
    protected $_matchedEntities = array(
        Magento_Catalog_Model_Product::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
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
        Magento_Core_Model_Config_Value::ENTITY => array(
            Magento_Index_Model_Event::TYPE_SAVE
        ),
    );

    protected $_relatedConfigSettings = array(
        Magento_Catalog_Helper_Category::XML_PATH_CATEGORY_URL_SUFFIX,
        Magento_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_SUFFIX,
        Magento_Catalog_Helper_Product::XML_PATH_PRODUCT_URL_USE_CATEGORY,
    );

    /**
     * Get Indexer name
     *
     * @return string
     */
    public function getName()
    {
        return __('Catalog URL Rewrites');
    }

    /**
     * Get Indexer description
     *
     * @return string
     */
    public function getDescription()
    {
        return __('Index product and categories URL Redirects');
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
        $data       = $event->getNewData();
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
        } else if ($entity == Magento_Core_Model_Store_Group::ENTITY) {
            $storeGroup = $event->getDataObject();
            $hasDataChanges = $storeGroup && ($storeGroup->dataHasChangedFor('root_category_id')
                || $storeGroup->dataHasChangedFor('website_id'));
            if ($storeGroup && !$storeGroup->isObjectNew() && $hasDataChanges) {
                $result = true;
            } else {
                $result = false;
            }
        } else if ($entity == Magento_Core_Model_Config_Value::ENTITY) {
            $configData = $event->getDataObject();
            if ($configData && in_array($configData->getPath(), $this->_relatedConfigSettings)) {
                $result = $configData->isValueChanged();
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
            case Magento_Core_Model_Config_Value::ENTITY:
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
        $product = $event->getDataObject();
        $dataChange = $product->dataHasChangedFor('url_key')
            || $product->getIsChangedCategories()
            || $product->getIsChangedWebsites();

        if (!$product->getExcludeUrlRewrite() && $dataChange) {
            $event->addNewData('rewrite_product_ids', array($product->getId()));
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
        if (!$category->getInitialSetupFlag() && $category->getLevel() > 1) {
            if ($category->dataHasChangedFor('url_key') || $category->getIsChangedProductList()) {
                $event->addNewData('rewrite_category_ids', array($category->getId()));
            }
            /**
             * Check if category has another affected category ids (category move result)
             */
            if ($category->getAffectedCategoryIds()) {
                $event->addNewData('rewrite_category_ids', $category->getAffectedCategoryIds());
            }
        }
    }

    /**
     * Process event
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        $data = $event->getNewData();
        if (!empty($data['catalog_url_reindex_all'])) {
            $this->reindexAll();
        }

        /* @var $urlModel Magento_Catalog_Model_Url */
        $urlModel = Mage::getSingleton('Magento_Catalog_Model_Url');

        // Force rewrites history saving
        $dataObject = $event->getDataObject();
        if ($dataObject instanceof Magento_Object && $dataObject->hasData('save_rewrites_history')) {
            $urlModel->setShouldSaveRewritesHistory($dataObject->getData('save_rewrites_history'));
        }

        if(isset($data['rewrite_product_ids'])) {
            $urlModel->clearStoreInvalidRewrites(); // Maybe some products were moved or removed from website
            foreach ($data['rewrite_product_ids'] as $productId) {
                 $urlModel->refreshProductRewrite($productId);
            }
        }
        if (isset($data['rewrite_category_ids'])) {
            $urlModel->clearStoreInvalidRewrites(); // Maybe some categories were moved
            foreach ($data['rewrite_category_ids'] as $categoryId) {
                $urlModel->refreshCategoryRewrite($categoryId);
            }
        }
    }

    /**
     * Rebuild all index data
     */
    public function reindexAll()
    {
        /** @var $resourceModel Magento_Catalog_Model_Resource_Url */
        $resourceModel = Mage::getResourceSingleton('Magento_Catalog_Model_Resource_Url');
        $resourceModel->beginTransaction();
        try {
            Mage::getSingleton('Magento_Catalog_Model_Url')->refreshRewrites();
            $resourceModel->commit();
        } catch (Exception $e) {
            $resourceModel->rollBack();
            throw $e;
        }
    }
}
