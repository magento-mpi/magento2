<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Helper\Product as ProductHelper;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product as ProductStorage;
use Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory;
use Magento\UrlRedirect\Service\V1\StorageInterface;

/**
 * Product Generator
 */
class Generator
{
    const URL_SLASH = '/';

    /**
     * @var \Magento\Catalog\Model\Product
     */
    protected $product;

    /**
     * @var \Magento\Catalog\Helper\Product
     */
    protected $productHelper;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories = null;

    /**
     * @var \Magento\UrlRedirect\Service\V1\StorageInterface
     */
    protected $storage;

    /**
     * @var \Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory
     */
    protected $filterFactory;

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\UrlRedirect\Service\V1\StorageInterface $storage
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\FilterFactory $filterFactory
     */
    public function __construct(Product $product, ProductHelper $productHelper, StorageInterface $storage, FilterFactory $filterFactory)
    {
        $this->product = $product;
        $this->productHelper = $productHelper;
        $this->storage = $storage;
        $this->filterFactory = $filterFactory;
    }

    /**
     * Generate list of urls per store
     *
     * @param int $storeId
     * @return \Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product[]
     */
    public function generatePerStore($storeId)
    {
        $urls = [];
        $urls[] = $this->buildProductStorageData($storeId, $this->getRequestPath($storeId), $this->getTargetPath(), $this->getEntityType());
        foreach ($this->getCategories() as $category) {
            $urls[] = $this->buildProductStorageData(
                $storeId,
                $this->getRequestPath($storeId, $category),
                $this->getTargetPath($category),
                $this->getEntityType()
            );
        }
        $urls = array_merge($urls, $this->buildPermanentRedirectUrls($storeId));
        return $urls;
    }

    /**
     * Build redirect url
     *
     * @param int $storeId
     * @return array
     */
    protected function buildPermanentRedirectUrls($storeId)
    {
        /** @var \Magento\UrlRedirect\Service\V1\Storage\Data\Filter $filter */
        $filter = $this->filterFactory->create();
        $filter->setEntityId($this->getEntityId());
        $entityTypes = [$this->getRedirectEntityType()];
        if ($this->isNeedCreatePermanentRedirect()) {
            $entityTypes[] = $this->getEntityType();
        }
        $filter->setEntityType($entityTypes);
        $filter->setStoreId($storeId);
        $previousUrls = $this->storage->findAllByFilter($filter);
        $urls = [];
        foreach ($previousUrls as $url) {
            $requestPath = $url->getRequestPath();
            if ($url->getEntityType() == $this->getEntityType()) {
                $targetPath = str_replace($this->getPreviousUrlKey(), $this->getUrlKey(), $requestPath);
            } else {
                $targetPath = str_replace($this->getPreviousUrlKey(), $this->getUrlKey(), $url->getTargetPath());
            }
            if ($requestPath == $targetPath) {
                continue;
            }
            $urls[] = $this->buildProductStorageData($storeId, $requestPath, $targetPath, $this->getRedirectEntityType(), 'RP');
        }
        return $urls;
    }

    /**
     * Get target path
     *
     * @param \Magento\Catalog\Model\Category|null $category
     * @return string
     */
    protected function getTargetPath(Category $category = null)
    {
        if ($category) {
            return 'catalog/product/view/id/' . $this->getEntityId() . '/category/' . $category->getId();
        }
        return 'catalog/product/view/id/' . $this->getEntityId();
    }

    /**
     * Get request path
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Category $category
     * @return string
     */
    protected function getRequestPath($storeId, Category $category = null)
    {
        $path = $this->getUrlKey() . $this->getUrlSuffix($storeId);
        if ($category) {
            $path = $category->getUrlPath() . self::URL_SLASH . $path;
        }
        return $path;
    }

    /**
     * @param string $storeId
     * @return string
     */
    protected function getUrlSuffix($storeId)
    {
        return $this->productHelper->getProductUrlSuffix($storeId);
    }

    /**
     * @return bool
     */
    protected function isNeedCreatePermanentRedirect()
    {
        return $this->product->getData('save_rewrites_history');
    }

    /**
     * Get entity url key
     *
     * @return string
     */
    protected function getUrlKey()
    {
        return $this->product->getData('url_key');
    }

    /**
     * Get entity previous url key
     *
     * @return string
     */
    protected function getPreviousUrlKey()
    {
        return $this->product->getOrigData('url_key');
    }

    /**
     * Get entity id
     *
     * @return int
     */
    protected function getEntityId()
    {
        return $this->product->getId();
    }

    /**
     * @return string
     */
    protected function getEntityType()
    {
        return ProductStorage::TYPE;
    }

    /**
     * @return string
     */
    protected function getRedirectEntityType()
    {
        return ProductStorage::TYPE_REDIRECT;
    }

    /**
     * Build product storage data
     *
     * @param int $storeId
     * @param string $requestPath
     * @param string $targetPath
     * @param string $entityType
     * @param string $redirectType
     * @return array
     */
    protected function buildProductStorageData($storeId, $requestPath, $targetPath, $entityType, $redirectType = '')
    {
        return [
            ProductStorage::ENTITY_TYPE => $entityType,
            ProductStorage::ENTITY_ID => $this->getEntityId(),
            ProductStorage::STORE_ID => $storeId,
            ProductStorage::REQUEST_PATH => $requestPath,
            ProductStorage::TARGET_PATH => $targetPath,
            ProductStorage::REDIRECT_TYPE => $redirectType,
        ];
    }

    /**
     * Get categories assigned to product
     *
     * @return \Magento\Catalog\Model\Resource\Category\Collection
     */
    protected function getCategories()
    {
        if (!$this->categories) {
            $this->categories = $this->product->getCategoryCollection();
            $this->categories->addAttributeToSelect('url_key');
            $this->categories->addAttributeToSelect('url_path');
        }
        return $this->categories;
    }
}
