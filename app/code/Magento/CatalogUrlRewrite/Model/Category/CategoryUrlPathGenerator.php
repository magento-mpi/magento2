<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

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

    /**
     * Scope config
     *
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    /**
     * Store manager
     *
     * @var \Magento\Store\Model\StoreManagerInterface
     */
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
     * Retrieve URL path
     *
     * @param \Magento\Catalog\Model\Category|\Magento\Framework\Object $category
     * @return string
     */
    public function getUrlPath($category)
    {
        $path = $category->getData('url_path');
        if ($path) {
            return $path;
        }

        $path = $category->getUrlKey();
        if ($category->getParentId()) {
            $parentPath = $this->categoryFactory->create()->load($category->getParentId())->getCategoryPath();
            $path = $parentPath . '/' . $path;
        }

        $category->setUrlPath($path);

        return $path;
    }

    /**
     * Get category url path
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    public function getUrlPathWithSuffix($category)
    {
        return $this->getUrlPath($category) . $this->getCategoryUrlSuffix($category->getStoreId());
    }

    /**
     * Retrieve clear url for category as parent
     *
     * @param string $urlPath
     * @param bool $slash
     * @param int $storeId
     * @return string
     */
    public function getUrlPathForStore($urlPath, $slash = false, $storeId = null)
    {
        if (!$this->getCategoryUrlSuffix($storeId)) {
            return $urlPath;
        }

        if ($slash) {
            $regexp = '#(' . preg_quote($this->getCategoryUrlSuffix($storeId), '#') . ')/$#i';
            $replace = '/';
        } else {
            $regexp = '#(' . preg_quote($this->getCategoryUrlSuffix($storeId), '#') . ')$#i';
            $replace = '';
        }

        return preg_replace($regexp, $replace, $urlPath);
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
        $parentPath = $this->getUrlPathForStore('', true, $category->getStoreId());
        $urlKey = $category->getUrlKey();
        return $parentPath . $category->formatUrlKey($urlKey === '' ? $category->getName() : $urlKey);
    }
}
