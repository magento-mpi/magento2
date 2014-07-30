<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Category;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\Catalog\Model\CategoryFactory;

class Remove
{
    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var FilterFactory */
    protected $filterFactory;

    /** @var  CategoryFactory */
    protected $categiryFactory;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        FilterFactory $filterFactory,
        CategoryFactory $categoryFactory
    ) {
        $this->urlPersist = $urlPersist;
        $this->filterFactory = $filterFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * Remove product urls from storage
     *
     * @param Category $category
     * @param mixed $result
     * @return mixed
     */
    public function afterDelete(Category $category, $result)
    {
        //@TODO BUG fix removing of Product Url Rewrites for category and category children
        /** @var Category $category */
        $categoryIds = explode(',', $category->getAllChildren());
        foreach ($categoryIds as $categoryId) {
            $this->deleteRewritesForCategory($categoryId);
        }

        return $result;
    }

    /***
     * @param int $categoryId
     */
    protected function deleteRewritesForCategory($categoryId)
    {
        $this->urlPersist->deleteByEntityData([
            UrlRewrite::ENTITY_ID => $categoryId,
            UrlRewrite::ENTITY_TYPE => CategoryUrlGenerator::ENTITY_TYPE_CATEGORY,
        ]);
    }
}
