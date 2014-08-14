<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory;

class ChildrenUrlRewriteGenerator
{
    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /** @var \Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory */
    protected $categoryUrlRewriteGeneratorFactory;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGeneratorFactory $categoryUrlRewriteGeneratorFactory
     * @param \Magento\CatalogUrlRewrite\Model\CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder $urlRewriteBuilder
     */
    public function __construct(
        CategoryUrlRewriteGeneratorFactory $categoryUrlRewriteGeneratorFactory,
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        UrlRewriteBuilder $urlRewriteBuilder
    ) {
        $this->categoryUrlRewriteGeneratorFactory = $categoryUrlRewriteGeneratorFactory;
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->urlRewriteBuilder = $urlRewriteBuilder;
    }

    /**
     * Generate list based on store view
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($storeId, Category $category)
    {
        $childrenUrls = array();
        //@TODO BUG getChildrenCategories() returns only categories with 'is_active' == 1
        foreach ($category->getChildrenCategories() as $childCategory) {
            $childCategory->setStoreId($storeId);
            $childCategory->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $childrenUrls = array_merge(
                $childrenUrls,
                $this->categoryUrlRewriteGeneratorFactory->create()->generate($childCategory)
            );
        }
        return $childrenUrls;
    }
}
