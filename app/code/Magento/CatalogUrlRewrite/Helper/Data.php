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
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite\Converter;

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

    /** @var \Magento\Catalog\Model\Resource\Url  */
    protected $catalogUrl;

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
     * @param \Magento\Framework\App\Resource $resource
     * @param ProductHelper $productHelper
     * @param CategoryHelper $categoryHelper
     * @param \Magento\Catalog\Model\Resource\Url $catalogUrl
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        ProductHelper $productHelper,
        CategoryHelper $categoryHelper,
        \Magento\Catalog\Model\Resource\Url $catalogUrl
    ) {
        $this->productHelper = $productHelper;
        $this->categoryHelper = $categoryHelper;
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        $this->catalogUrl = $catalogUrl;
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
     * TODO: it is draft method, do not use in production (@TODO: UrlRewrite)
     * @param Category $category
     * @return string
     */
    public function getCategoryUrlKeyPath(Category $category)
    {
        $parentUrlPath = $this->catalogUrl
            ->getCategory($category->getParentId(), $category->getStoreId())
            ->getUrlPath();
        $requestPath = trim($parentUrlPath . '/' . $category->getUrlKey(), '/');
        $category->setUrlPath($requestPath);
        $this->catalogUrl->saveCategoryAttribute($category, 'url_path');
        return $requestPath . $this->categoryHelper->getCategoryUrlSuffix($category->getStoreId());
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
}
