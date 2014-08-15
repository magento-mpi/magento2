<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory;

class ChildrenUrlRewriteGenerator
{
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory */
    protected $categoryUrlRewriteGeneratorFactory;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory $categoryUrlRewriteGeneratorFactory
     */
    public function __construct(
        CategoryUrlRewriteGeneratorFactory $categoryUrlRewriteGeneratorFactory
    ) {
        $this->categoryUrlRewriteGeneratorFactory = $categoryUrlRewriteGeneratorFactory;
    }

    /**
     * Generate list of children urls
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($storeId, Category $category)
    {
        $urls = [];
        //@TODO BUG getChildrenCategories() returns only categories with 'is_active' == 1
        foreach ($category->getChildrenCategories() as $childCategory) {
            $childCategory->setStoreId($storeId);
            $childCategory->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $urls = array_merge(
                $urls,
                $this->categoryUrlRewriteGeneratorFactory->create()->generate($childCategory)
            );
        }
        return $urls;
    }
}
