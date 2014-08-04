<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model;

/**
 * @TODO: UrlRewrite: split this class by different responsibilities
 */
class CategoryUrlPathGenerator
{
    const XML_PATH_CATEGORY_URL_SUFFIX = 'catalog/seo/category_url_suffix';

    /**
     * Cache for category rewrite suffix
     *
     * @var array
     */
    protected $categoryUrlSuffix = array();

    /** @var \Magento\Framework\App\Config\ScopeConfigInterface */
    protected $scopeConfig;

    /** @var \Magento\Store\Model\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\Catalog\Model\CategoryFactory */
    protected $categoryFactory;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Catalog\Model\CategoryFactory $categoryFactory
    ) {
        $this->storeManager = $storeManager;
        $this->scopeConfig = $scopeConfig;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Build category URL path
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Framework\Object $category
     * @return string
     */
    public function getUrlPath($category)
    {
        if ($category->getParentId() == 1) {
            return '';
        }
        $path = $category->getData('url_path');
        if ($path !== null && !$category->dataHasChangedFor('url_key') && !$category->dataHasChangedFor('path_ids')) {
            return $path;
        }
        $path = $category->getUrlKey();
        if ($category->getParentId()) {
            $parentPath = $this->getUrlPath($this->categoryFactory->create()->load($category->getParentId()));
            $path = $parentPath === '' ? $path : $parentPath . '/' . $path;
        }
        return $path;
    }

    /**
     * Get category url path
     *
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return string
     */
    public function getUrlPathWithSuffix($category, $storeId = null)
    {
        if ($storeId === null) {
            $storeId = $category->getStoreId();
        }
        return $this->getUrlPath($category) . $this->getCategoryUrlSuffix($storeId);
    }

    /**
     * Retrieve category rewrite suffix for store
     *
     * @param int $storeId
     * @return string
     */
    protected function getCategoryUrlSuffix($storeId = null)
    {
        if ($storeId === null) {
            $storeId = $this->storeManager->getStore()->getId();
        }
        if (!isset($this->categoryUrlSuffix[$storeId])) {
            $this->categoryUrlSuffix[$storeId] = $this->scopeConfig->getValue(
                self::XML_PATH_CATEGORY_URL_SUFFIX,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
                $storeId
            );
        }
        return $this->categoryUrlSuffix[$storeId];
    }

    /**
     * Get canonical category url
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getCanonicalUrlPath($category)
    {
        return 'catalog/category/view/id/' . $category->getId();
    }

    /**
     * Generate category url key
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function generateUrlKey($category)
    {
        $urlKey = $category->getUrlKey();
        return $category->formatUrlKey($urlKey == '' ? $category->getName() : $urlKey);
    }
}
