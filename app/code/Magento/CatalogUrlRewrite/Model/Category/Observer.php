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

    /** var \Magento\CatalogUrlRewrite\Helper\Data */
    protected $catalogUrlRewriteHelper;

    /**
     * @var FilterFactory
     */
    protected $filterFactory;

    /**
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\CatalogUrlRewrite\Helper\Data $catalogUrlRewriteHelper
     * @param FilterFactory $filterFactory
     */
    public function __construct(
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator,
        UrlPersistInterface $urlPersist,
        \Magento\CatalogUrlRewrite\Helper\Data $catalogUrlRewriteHelper,
        FilterFactory $filterFactory
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlPersist = $urlPersist;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;// TODO: MAGETWO-26285
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
        $urls = array();
        if ($category->dataHasChangedFor('url_key')) {
            $urls = array_merge(
                $this->categoryUrlGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
        } elseif ($category->dataHasChangedFor('affected_product_ids')) {
            $urls = $this->generateProductUrlRewrites($category);
        }

        $filter = $this->filterFactory->create(['filterData' => [
            UrlRewrite::ENTITY_ID => $category->getId(),
            UrlRewrite::ENTITY_TYPE => CategoryUrlGenerator::ENTITY_TYPE_CATEGORY,
        ]]);
        $this->urlPersist->deleteByFilter($filter);
        if ($urls) {
            $this->urlPersist->save($urls);
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
            //@TODO remove it when fix empty url_path for product
            $product->setUrlPath($this->catalogUrlRewriteHelper->generateProductUrlKeyPath($product));

            $product->setStoreId($category->getStoreId());
            $product->setStoreIds($category->getStoreIds());
            $product->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $productUrls = array_merge($productUrls, $this->productUrlGenerator->generate($product));

            $filter = $this->filterFactory->create(['filterData' => [
                UrlRewrite::ENTITY_ID => $product->getId(),
                UrlRewrite::ENTITY_TYPE => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
            ]]);
            $this->urlPersist->deleteByFilter($filter);
        }
        return $productUrls;
    }
}
