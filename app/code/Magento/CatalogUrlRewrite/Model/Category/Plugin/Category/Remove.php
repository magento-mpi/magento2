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
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\Store;

class Remove
{
    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var FilterFactory */
    protected $filterFactory;

    /** @var  CategoryFactory */
    protected $categoryFactory;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param FilterFactory $filterFactory
     * @param CategoryFactory $categoryFactory
     * @param ProductUrlGenerator $productUrlGenerator
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        FilterFactory $filterFactory,
        CategoryFactory $categoryFactory,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->urlPersist = $urlPersist;
        $this->filterFactory = $filterFactory;
        $this->categoryFactory = $categoryFactory;
        $this->productUrlGenerator = $productUrlGenerator;
    }

    /**
     * Remove product urls from storage
     *
     * @param Category $category
     * @param callable $proceed
     * @return mixed
     */
    public function aroundDelete(Category $category, \Closure $proceed)
    {
        $categoryIds = explode(',', $category->getAllChildren());
        $result = $proceed();
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
        $collection = $this->categoryFactory->create()
            ->load($categoryId)
            ->getProductCollection()
            ->addAttributeToSelect(['url_key', 'url_path', 'name']);
        $productUrls = [];
        foreach ($collection as $product) {
            $product->setStoreId(Store::DEFAULT_STORE_ID);
            $productUrls = array_merge($productUrls, $this->productUrlGenerator->generate($product));
        }
        $this->urlPersist->replace($productUrls);
    }
}
