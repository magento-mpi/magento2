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

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Resource\Category\Collection as CategoryCollection;
use Magento\Catalog\Model\Resource\Category\Flat\Collection as FlatCollection;
use Magento\Catalog\Model\Resource\Product\Collection as ProductCollection;
use Magento\Core\Model\AbstractModel;

class Index extends AbstractModel
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Magento\CatalogPermissions\Model\Resource\Permission\Index');
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
     * @param ProductCollection $collection
     * @param int $customerGroupId
     * @return $this
     */
    public function addIndexToProductCount($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCount($collection, $customerGroupId);
        return $this;
    }

    /**
     * Add index to category collection
     *
     * @param CategoryCollection|FlatCollection $collection
     * @param int $customerGroupId
     * @param int $websiteId
     * @return $this
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
     * @return $this
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
     * @param ProductCollection $collection
     * @param int $customerGroupId
     * @return $this
     */
    public function addIndexToProductCollection($collection, $customerGroupId)
    {
        $this->getResource()->addIndexToProductCollection($collection, $customerGroupId);
        return $this;
    }

     /**
     * Add permission index to product model
     *
     * @param Product $product
     * @param int $customerGroupId
     * @return $this
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
}
