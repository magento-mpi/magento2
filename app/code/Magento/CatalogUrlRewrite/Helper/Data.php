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
use Magento\UrlRedirect\Service\V1\Data\Converter;
use Magento\UrlRedirect\Service\V1\Data\UrlRewrite;

/**
 * Helper Data
 */
class Data
{
    /**
     * Url slash
     */
    const URL_SLASH = '/';

    /**
     * @var \Magento\Eav\Model\Config
     */
    protected $eavConfig;

    /**
     * @var false|\Magento\Framework\DB\Adapter\AdapterInterface
     */
    protected $connection;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

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
     * @param Config $eavConfig
     * @param Resource $resource
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param StoreManagerInterface $storeManager
     * @param CategoryHelper $categoryHelper
     * @param Converter $converter
     */
    public function __construct(
        Config $eavConfig,
        Resource $resource,
        ProductHelper $productHelper,
        StoreManagerInterface $storeManager,
        CategoryHelper $categoryHelper,
        Converter $converter
    ) {
        $this->eavConfig = $eavConfig;
        $this->connection = $resource->getConnection(Resource::DEFAULT_READ_RESOURCE);
        $this->productHelper = $productHelper;
        $this->storeManager = $storeManager;
        $this->categoryHelper = $categoryHelper;
        $this->converter = $converter;
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

    /**
     * Whether the store is default
     *
     * @param int|null $storeId
     * @return bool
     */
    public function isDefaultStore($storeId)
    {
        return null === $storeId || $storeId == Store::DEFAULT_STORE_ID;
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
     * Check is root category
     *
     * @param Category $category
     * @return string
     */
    public function isRootCategory(Category $category)
    {
        $store = $this->storeManager->getStore($category->getStoreId());

        return $category->getId() == $store->getRootCategoryId();
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
     * Create url rewrite object
     *
     * @param string $entityType
     * @param int $entityId
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param string|null $redirectType Null or one of OptionProvider const
     * @return UrlRewrite
     */
    public function createUrlRewrite(
        $entityType,
        $entityId,
        $storeId,
        $requestPath,
        $targetPath,
        $redirectType = null
    ) {
        return $this->converter->convertArrayToObject([
            UrlRewrite::ENTITY_TYPE => $entityType,
            UrlRewrite::ENTITY_ID => $entityId,
            UrlRewrite::STORE_ID => $storeId,
            UrlRewrite::REQUEST_PATH => $requestPath,
            UrlRewrite::TARGET_PATH => $targetPath,
            UrlRewrite::REDIRECT_TYPE => $redirectType,
        ]);
    }
}
