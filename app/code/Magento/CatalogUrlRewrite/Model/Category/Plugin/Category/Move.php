<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Category;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator;

class Move
{
    /** @var CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /**
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     */
    public function __construct(CategoryUrlPathGenerator $categoryUrlPathGenerator)
    {
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
    }

    /**
     * @param \Magento\Catalog\Model\Resource\Category $subject
     * @param callable $proceed
     * @param $category
     * @param $newParent
     * @param $afterCategoryId
     * @return callable
     */
    public function aroundChangeParent(
        \Magento\Catalog\Model\Resource\Category $subject,
        \Closure $proceed,
        $category,
        $newParent,
        $afterCategoryId
    ) {
        $result = $proceed($category, $newParent, $afterCategoryId);
        $category->setUrlKey($this->categoryUrlPathGenerator->generateUrlKey($category))
            ->setUrlPath($this->categoryUrlPathGenerator->getUrlPath($category));
        return $result;
    }
}
