<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\FilterFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;

class Observer
{
    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var FilterFactory */
    protected $filterFactory;

    /**
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     * @param UrlPersistInterface $urlPersist
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator,
        UrlPersistInterface $urlPersist,
        FilterFactory $filterFactory
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlPersist = $urlPersist;
        $this->filterFactory = $filterFactory;
    }

    /**
     * Generate urls for UrlRewrite and save it in storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteSaving(EventObserver $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        if ($category->getParentId() == Category::TREE_ROOT_ID) {
            return;
        }
        $urlRewrites = [];
        if ($category->dataHasChangedFor('url_key')) {
            $urlRewrites = $this->categoryUrlGenerator->generate($category);
        } elseif ($category->dataHasChangedFor('parent_id')) {
            //@TODO verify should we save rewrites history
            //@TODO when perform move action does not call category_url_path_autogeneration on catalog_category_save_before event
            $urlRewrites = array_merge(
                $this->categoryUrlGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
            $ids = explode(',', $category->getAllChildren());
            //@TODO BUG fix generation of Product Url Rewrites for children
            //@TODO Verify case when moved to root category without assignment to store(Should we delete custom rewrites?)
            foreach ($ids as $id) {
                $this->deleteRewritesForCategory($id);
            }
        }
        if ($category->dataHasChangedFor('affected_product_ids')) {
            $urlRewrites = array_merge($urlRewrites, $this->generateProductUrlRewrites($category));
        }
        if ($urlRewrites) {
            $this->urlPersist->replace($urlRewrites);
        }
    }

    /**
     * Generate url rewrites for products assigned to category
     *
     * @param Category $category
     * @return array
     */
    protected function generateProductUrlRewrites(Category $category)
    {
        $collection = $category->getProductCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $productUrls = [];
        foreach ($collection as $product) {
            $product->setStoreId($category->getStoreId());
            $product->setStoreIds($category->getStoreIds());
            $product->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $productUrls = array_merge($productUrls, $this->productUrlGenerator->generate($product));
        }
        return $productUrls;
    }

    /***
     * @param int $categoryId
     */
    protected function deleteRewritesForCategory($categoryId)
    {
        $filter = $this->filterFactory->create(
            [
                'filterData' => [
                    UrlRewrite::ENTITY_ID => $categoryId,
                    UrlRewrite::ENTITY_TYPE => CategoryUrlGenerator::ENTITY_TYPE_CATEGORY,
                ]
            ]
        );
        $this->urlPersist->deleteByFilter($filter);
    }

    /**
     * Remove product urls from storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteRemoving(EventObserver $observer)
    {
        //@TODO BUG fix removing of Product Url Rewrites for category and category children
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        $ids = explode(',', $category->getAllChildren());
        if ($ids) {
            foreach ($ids as $id) {
                $this->deleteRewritesForCategory($id);
            }
        }
    }
}
