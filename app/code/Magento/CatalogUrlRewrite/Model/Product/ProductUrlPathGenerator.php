<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

/**
 * @TODO: UrlRewrite: split this class by different responsibilities
 */
class ProductUrlPathGenerator
{
    const XML_PATH_PRODUCT_URL_SUFFIX = 'catalog/seo/product_url_suffix';

    /**
     * Cache for product rewrite suffix
     *
     * @var array
     */
    protected $productUrlSuffix = array();

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator $categoryUrlPathGenerator
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * Retrieve Product Url path (with category if exists)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     *
     * @return string
     */
    public function getUrlPath($product, $category = null)
    {
        $path = $product->getData('url_path');
        if ($path === null) {
            $path = $this->generateUrlKey($product);
        }
        return $category === null ? $path
            : $this->categoryUrlPathGenerator->getUrlPathForStore(
                $this->categoryUrlPathGenerator->getUrlPath($category)
            ) . '/' . $path;
    }

    /**
     * Retrieve Product Url path with suffix
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param int $storeId
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getUrlPathWithSuffix($product, $storeId, $category = null)
    {
        return $this->getUrlPath($product, $category) . $this->getProductUrlSuffix($storeId);
    }

    /**
     * Get canonical product url path
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function getCanonicalUrlPath($product)
    {
        return 'catalog/product/view/id/' . $product->getId();
    }

    /**
     * Get canonical product url path with category
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getCanonicalUrlPathWithCategory($product, $category)
    {
        return $this->getCanonicalUrlPath($product) . '/category/' . $category->getId();
    }

    /**
     * Generate product url key based on url_key entered by merchant or product name
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return string
     */
    public function generateUrlKey($product)
    {
        $urlKey = $product->getUrlKey();
        return $product->formatUrlKey($urlKey === '' ? $product->getName() : $urlKey);
    }

    /**
     * Retrieve product rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getProductUrlSuffix($storeId = null)
    {
        if (is_null($storeId)) {
            $storeId = $this->storeManager->getStore()->getId();
        }

        if (!isset($this->productUrlSuffix[$storeId])) {
            $this->productUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                self::XML_PATH_PRODUCT_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->productUrlSuffix[$storeId];
    }
}
