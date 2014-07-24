<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Helper;

use Magento\Catalog\Helper\Category as CategoryHelper;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\Product;
use Magento\Eav\Model\Config;
use Magento\Framework\App\Resource;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Service\V1\Data\Converter;

/**
 * TODO: It is stub class (UrlRewrite)
 *
 * Helper Data
 */
class Data
{
    /**
     * Url slash
     */
    const URL_SLASH = '/';

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * Catalog category helper
     *
     * @var CategoryHelper
     */
    protected $categoryHelper;

    /**
     * @var Converter
     */
    protected $converter;

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @param Config $eavConfig
     * @param Resource $resource
     * @param ProductHelper $productHelper
     * @param CategoryHelper $categoryHelper
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        ProductHelper $productHelper,
        CategoryHelper $categoryHelper
    ) {
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
    }

    /**
     * Get canonical product url path
     *
     * @param Product $product
     * @return string
     */
    public function getProductCanonicalUrlPath(Product $product)
    {
        // TODO: see $product->getUrlModel() @TODO: UrlRewrite
        return 'catalog/product/view/id/' . $product->getId();
    }

    /**
     * Get canonical product url path with category
     *
     * @param Product $product
     * @param Category $category
     * @return string
     */
    public function getProductCanonicalUrlPathWithCategory(Product $product, Category $category)
    {
        return $this->getProductCanonicalUrlPath($product) . '/category/' . $category->getId();
    }

    /**
     * Get product url key path
     *
     * TODO: decomposition of url model (@TODO: UrlRewrite)
     *
     * @param Product $product
     * @param int $storeId
     * @return string
     */
    public function getProductUrlKeyPath(Product $product, $storeId)
    {
        return $product->getUrlModel()->getUrlPath($product) . $this->productHelper->getProductUrlSuffix($storeId);
    }

    /**
     * Get product url key path with category
     *
     * TODO: decomposition of url model (@TODO: UrlRewrite)
     *
     * @param Product $product
     * @param Category $category
     * @param int $storeId
     * @return string
     */
    public function getProductUrlKeyPathWithCategory(Product $product, Category $category, $storeId)
    {
        return $product->getUrlModel()->getUrlPath($product, $category)
        . $this->productHelper->getProductUrlSuffix($storeId);
    }

    /**
     * Get canonical category url
     *
     * TODO: see \Magento\Catalog\Model\Category::getCategoryIdUrl() (@TODO: UrlRewrite)
     *
     * @param Category $category
     * @return string
     */
    public function getCategoryCanonicalUrlPath(Category $category)
    {
        return 'catalog/category/view/id/' . $category->getId();
    }

    /**
     * Get category url path
     *
     * @param Category $category
     * @return string
     */
    public function getCategoryUrlKeyPath(Category $category)
    {
        return $category->getUrlPath();
    }

    /**
     * Generate category url key path
     *
     * TODO: it is draft method, do not use in production (@TODO: UrlRewrite)
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function generateCategoryUrlKeyPath($category)
    {
        $parentPath = $this->categoryHelper->getCategoryUrlPath('', true, $category->getStoreId());

        $urlKey = $category->getUrlKey() == ''
            ? $category->formatUrlKey($category->getName()) : $category->formatUrlKey($category->getUrlKey());

        return $parentPath . $urlKey;
    }

    /**
     * Generate product url key path
     *
     * TODO: it is draft method, do not use in production (@TODO: UrlRewrite)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function generateProductUrlKeyPath($product)
    {
        // TODO: product prefix (@TODO: UrlRewrite)

        $urlKey = $product->getUrlKey() == ''
            ? $product->formatUrlKey($product->getName())
            : $product->formatUrlKey($product->getUrlKey());

        return $urlKey;
    }

    /**
     * If product saved on default store view, then need to check specific url_key for other stores
     *
     * @param int $storeId
     * @param int $productId
     * @return bool
     */
    public function isNeedCreateUrlRewrite($storeId, $productId)
    {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, 'url_key');
        $select = $this->connection->select()
            ->from($attribute->getBackendTable(), 'store_id')
            ->where('attribute_id = ?', $attribute->getId())
            ->where('entity_id = ?', $productId);

        return !in_array($storeId, $this->connection->fetchCol($select));
    }
}
