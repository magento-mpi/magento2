<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission indexer
 *
 * @method Enterprise_CatalogPermissions_Model_Resource_Permission_Index _getResource()
 * @method Enterprise_CatalogPermissions_Model_Resource_Permission_Index getResource()
 * @method int getCategoryId()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setCategoryId(int $value)
 * @method int getWebsiteId()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setCustomerGroupId(int $value)
 * @method int getGrantCatalogCategoryView()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setGrantCatalogCategoryView(int $value)
 * @method int getGrantCatalogProductPrice()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setGrantCatalogProductPrice(int $value)
 * @method int getGrantCheckoutItems()
 * @method Enterprise_CatalogPermissions_Model_Permission_Index setGrantCheckoutItems(int $value)
 *
 * @category    Enterprise
 * @package     Enterprise_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_CatalogPermissions_Model_Permission_Index extends Magento_Index_Model_Indexer_Abstract
{
    /**
     * Reindex products permissions event type
     */
    const EVENT_TYPE_REINDEX_PRODUCTS = 'reindex_permissions';

    /**
     * Category entity for indexers
     */
    const ENTITY_CATEGORY = 'catalogpermissions_category';

    /**
     * Product entity for indexers
     */
    const ENTITY_PRODUCT = 'catalogpermissions_product';

    /**
     * Config entity for indexers
     */
    const ENTITY_CONFIG = 'catalogpermissions_config';

    /**
     * Matched entities
     *
     * @var array
     */
    protected $_matchedEntities = array(
        self::ENTITY_PRODUCT  => array(self::EVENT_TYPE_REINDEX_PRODUCTS),
        self::ENTITY_CATEGORY => array(self::EVENT_TYPE_REINDEX_PRODUCTS),
        self::ENTITY_CONFIG   => array(Magento_Index_Model_Event::TYPE_SAVE),
    );

    /**
     * Disable visibility of the index
     *
     * @var bool
     */
    protected $_isVisible = false;

    protected function _construct()
    {
        $this->_init('Enterprise_CatalogPermissions_Model_Resource_Permission_Index');
    }

    /**
     * Reindex category permissions
     *
     * @param string $categoryPath
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function reindex($categoryPath)
    {
        $this->getResource()->reindex($categoryPath);
        return $this;
    }

    /**
     * Reindex products permissions
     *
     * @param array|string $productIds
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function reindexProducts($productIds = null)
    {
        $this->getResource()->reindexProducts($productIds);
        return $this;
    }

    /**
     * Reindex products permissions for standalone mode
     *
     * @param array|string $productIds
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function reindexProductsStandalone($productIds = null)
    {
        $this->getResource()->reindexProductsStandalone($productIds);
        return $this;
    }

    /**
     * Retrieve permission index for category or categories with specified customer group and website id
     *
     * @param int|array $categoryId
     * @param int|null $customerGroupId
     * @param int $websiteId
     * @return array
     */
    public function getIndexForCategory($categoryId, $customerGroupId, $websiteId)
    {
        return $this->getResource()->getIndexForCategory($categoryId, $customerGroupId, $websiteId);
    }

    /**
     * Add index to product count select in product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function addIndexToProductCount($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCount($collection, $customerGroupId);
        return $this;
    }

    /**
     * Add index to category collection
     *
     * @param Magento_Catalog_Model_Resource_Category_Collection|Magento_Catalog_Model_Resource_Category_Flat_Collection $collection
     * @param int $customerGroupId
     * @param int $websiteId
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function addIndexToCategoryCollection($collection, $customerGroupId, $websiteId)
    {
        $this->getResource()->addIndexToCategoryCollection($collection, $customerGroupId, $websiteId);
        return $this;
    }

    /**
     * Apply price grant on price index select
     *
     * @param Magento_Object $data
     * @param int $customerGroupId
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function applyPriceGrantToPriceIndex($data, $customerGroupId)
    {
        $this->getResource()->applyPriceGrantToPriceIndex($data, $customerGroupId);
        return $this;
    }

    /**
     * Retrieve restricted category ids for customer group and website
     *
     * @param int $customerGroupId
     * @param int $websiteId
     * @return array
     */
    public function getRestrictedCategoryIds($customerGroupId, $websiteId)
    {
        return $this->getResource()->getRestrictedCategoryIds($customerGroupId, $websiteId);
    }


    /**
     * Add index select in product collection
     *
     * @param Magento_Catalog_Model_Resource_Product_Collection $collection
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function addIndexToProductCollection($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCollection($collection, $customerGroupId);
        return $this;
    }

     /**
     * Add permission index to product model
     *
     * @param Magento_Catalog_Model_Product $product
     * @param int $customerGroupId
     * @return Enterprise_CatalogPermissions_Model_Permission_Index
     */
    public function addIndexToProduct($product, $customerGroupId)
    {
        $this->getResource()->addIndexToProduct($product, $customerGroupId);
        return $this;
    }

    /**
     * Get permission index for products
     *
     * @param int|array $productId
     * @param int $customerGroupId
     * @param int $storeId
     * @return array
     */
    public function getIndexForProduct($productId, $customerGroupId, $storeId)
    {
        return $this->getResource()->getIndexForProduct($productId, $customerGroupId, $storeId);
    }

    /**
     * Get name of the index
     *
     * @return string
     */
    public function getName()
    {
        return Mage::helper('Enterprise_CatalogPermissions_Helper_Data')->__('Catalog Permissions');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _registerEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case self::EVENT_TYPE_REINDEX_PRODUCTS:
                switch ($event->getEntity()) {
                    case self::ENTITY_PRODUCT:
                        $event->addNewData('product_ids', $event->getDataObject()->getId());
                        break;
                    case self::ENTITY_CATEGORY:
                        $event->addNewData('category_path', $event->getDataObject()->getId());
                        break;
                }
                break;
        }
    }

    /**
     * Process event based on event state data
     *
     * @param Magento_Index_Model_Event $event
     */
    protected function _processEvent(Magento_Index_Model_Event $event)
    {
        switch ($event->getType()) {
            case self::EVENT_TYPE_REINDEX_PRODUCTS:
                switch ($event->getEntity()) {
                    case self::ENTITY_PRODUCT:
                        $data = $event->getNewData();
                        if ($data['product_ids']) {
                            $this->reindexProducts($data['product_ids']);
                        }
                        break;
                    case self::ENTITY_CATEGORY:
                        $data = $event->getNewData();
                        if ($data['category_path']) {
                            $this->reindex($data['category_path']);
                        }
                        break;
                }
                break;
            case Magento_Index_Model_Event::TYPE_SAVE:
                switch ($event->getEntity()) {
                    case self::ENTITY_CONFIG:
                        $this->reindexProductsStandalone();
                        break;
                }
                break;
        }
    }
}
