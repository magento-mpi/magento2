<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Permission indexer
 *
 * @method \Magento\CatalogPermissions\Model\Resource\Permission\Index _getResource()
 * @method \Magento\CatalogPermissions\Model\Resource\Permission\Index getResource()
 * @method int getCategoryId()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setCategoryId(int $value)
 * @method int getWebsiteId()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setWebsiteId(int $value)
 * @method int getCustomerGroupId()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setCustomerGroupId(int $value)
 * @method int getGrantCatalogCategoryView()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setGrantCatalogCategoryView(int $value)
 * @method int getGrantCatalogProductPrice()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setGrantCatalogProductPrice(int $value)
 * @method int getGrantCheckoutItems()
 * @method \Magento\CatalogPermissions\Model\Permission\Index setGrantCheckoutItems(int $value)
 *
 * @category    Magento
 * @package     Magento_CatalogPermissions
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\CatalogPermissions\Model\Permission;

class Index extends \Magento\Index\Model\Indexer\AbstractIndexer
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
        self::ENTITY_CONFIG   => array(\Magento\Index\Model\Event::TYPE_SAVE),
    );

    /**
     * Disable visibility of the index
     *
     * @var bool
     */
    protected $_isVisible = false;

    protected function _construct()
    {
        $this->_init('\Magento\CatalogPermissions\Model\Resource\Permission\Index');
    }

    /**
     * Reindex category permissions
     *
     * @param string $categoryPath
     * @return \Magento\CatalogPermissions\Model\Permission\Index
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
     * @return \Magento\CatalogPermissions\Model\Permission\Index
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
     * @return \Magento\CatalogPermissions\Model\Permission\Index
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
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return \Magento\CatalogPermissions\Model\Permission\Index
     */
    public function addIndexToProductCount($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCount($collection, $customerGroupId);
        return $this;
    }

    /**
     * Add index to category collection
     *
     * @param \Magento\Catalog\Model\Resource\Category\Collection|\Magento\Catalog\Model\Resource\Category\Flat\Collection $collection
     * @param int $customerGroupId
     * @param int $websiteId
     * @return \Magento\CatalogPermissions\Model\Permission\Index
     */
    public function addIndexToCategoryCollection($collection, $customerGroupId, $websiteId)
    {
        $this->getResource()->addIndexToCategoryCollection($collection, $customerGroupId, $websiteId);
        return $this;
    }

    /**
     * Apply price grant on price index select
     *
     * @param \Magento\Object $data
     * @param int $customerGroupId
     * @return \Magento\CatalogPermissions\Model\Permission\Index
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
     * @param \Magento\Catalog\Model\Resource\Product\Collection $collection
     * @return \Magento\CatalogPermissions\Model\Permission\Index
     */
    public function addIndexToProductCollection($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCollection($collection, $customerGroupId);
        return $this;
    }

     /**
     * Add permission index to product model
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $customerGroupId
     * @return \Magento\CatalogPermissions\Model\Permission\Index
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
        return __('Catalog Permissions');
    }

    /**
     * Register indexer required data inside event object
     *
     * @param \Magento\Index\Model\Event $event
     */
    protected function _registerEvent(\Magento\Index\Model\Event $event)
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
     * @param \Magento\Index\Model\Event $event
     */
    protected function _processEvent(\Magento\Index\Model\Event $event)
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
            case \Magento\Index\Model\Event::TYPE_SAVE:
                switch ($event->getEntity()) {
                    case self::ENTITY_CONFIG:
                        $this->reindexProductsStandalone();
                        break;
                }
                break;
        }
    }
}
