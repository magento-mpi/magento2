<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Category;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;
use Magento\CatalogUrlRewrite\Model\ObjectRegistry;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\Framework\StoreManagerInterface;

class CategoriesUrlRewriteGenerator
{
    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\ObjectRegistry */
    protected $productCategories;

    /** @var \Magento\Framework\StoreManagerInterface */
    protected $storeManager;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder $urlRewriteBuilder
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ProductUrlPathGenerator $productUrlPathGenerator,
        UrlRewriteBuilder $urlRewriteBuilder,
        StoreManagerInterface $storeManager
    ) {
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->urlRewriteBuilder = $urlRewriteBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Generate list based on categories
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\CatalogUrlRewrite\Model\ObjectRegistry $productCategories
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($storeId, Product $product, ObjectRegistry $productCategories)
    {
        $this->productCategories = $productCategories;
        $urls = [];
        foreach ($this->productCategories->getList() as $category) {
            if ($this->isCategoryProperForGenerating($category, $storeId)) {
                $urls[] = $this->urlRewriteBuilder->setStoreId($storeId)
                    ->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                    ->setEntityId($product->getId())
                    ->setRequestPath(
                        $this->productUrlPathGenerator->getUrlPathWithSuffix($product, $storeId, $category)
                    )->setTargetPath($this->productUrlPathGenerator->getCanonicalUrlPath($product, $category))
                    ->setMetadata(['category_id' => $category->getId()])
                    ->create();
            }
        }
        return $urls;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param int $storeId
     * @return bool
     */
    protected function isCategoryProperForGenerating($category, $storeId)
    {
        if ($category->getParentId() != Category::TREE_ROOT_ID) {
            list(, $rootCategoryId) = $category->getParentIds();
            return $rootCategoryId == $this->storeManager->getStore($storeId)->getRootCategoryId();
        }
        return false;
    }
}
