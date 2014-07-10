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
use Magento\UrlRedirect\Service\V1\Storage\Data\Converter;

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
     * @var \Magento\UrlRedirect\Service\V1\Storage\Data\Converter
     */
    protected $converter;

    /**
     * @var null|\Magento\Catalog\Model\Resource\Category\Collection
     */
    protected $categories = null;

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Helper\Product $productHelper
     * @param \Magento\UrlRedirect\Service\V1\Storage\Data\Converter $converter
     */
    public function __construct(Product $product, ProductHelper $productHelper, Converter $converter)
    {
        $this->product = $product;
        $this->converter = $converter;
        $this->productHelper = $productHelper;
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
        $urls[] = $this->buildProductStorageData($storeId, $this->getRequestPath($storeId), $this->getTargetPath());
        foreach ($this->getCategories() as $category) {
            $urls[] = $this->buildProductStorageData(
                $storeId,
                $this->getRequestPath($storeId, $category),
                $this->getTargetPath($category)
            );
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
            return 'catalog/product/view/id/' . $this->product->getId() . '/category/' . $category->getId();
        }
        return 'catalog/product/view/id/' . $this->product->getId();
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
        $path = $this->getUrl($storeId);
        if ($category) {
            $path = $category->getUrlPath() . self::URL_SLASH . $path;
        }
        return $path;
    }

    /**
     * Get url
     *
     * @param int $storeId
     * @return string
     */
    protected function getUrl($storeId)
    {
        return $this->product->getUrlKey() . $this->productHelper->getProductUrlSuffix($storeId);
    }

    /**
     * Build product storage data
     *
     * @param $storeId
     * @param $requestPath
     * @param $targetPath
     * @return \Magento\CatalogUrlRewrite\Service\V1\Storage\Data\Product
     */
    protected function buildProductStorageData($storeId, $requestPath, $targetPath)
    {
        return $this->converter->getBuilder()
            ->setEntityId($this->product->getId())
            ->setEntityType(ProductStorage::ENTITY_TYPE)
            ->setRequestPath($requestPath)
            ->setTargetPath($targetPath)
            ->setStoreId($storeId)
            ->create();
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
