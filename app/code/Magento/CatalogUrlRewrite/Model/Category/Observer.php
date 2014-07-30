<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Block\Adminhtml\Form\Renderer\Attribute\Urlkey;

class Observer
{
    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     * @param UrlPersistInterface $urlPersist
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator,
        UrlPersistInterface $urlPersist,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlPersist = $urlPersist;
        $this->catalogData = $scopeConfig;
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
        if ($category->dataHasChangedFor('url_key') || $category->dataHasChangedFor('affected_product_ids')) {
            $urlRewrites = array_merge(
                $this->categoryUrlGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
            $this->urlPersist->replace($urlRewrites);
        }
    }

    /**
     * @param EventObserver $observer
     * @return void
     */
    public function processUrlRewriteMoving(EventObserver $observer)
    {
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        if ($category->dataHasChangedFor('parent_id')) {
            $saveRewritesHistory = $this->scopeConfig->isSetFlag(
                Urlkey::XML_PATH_SEO_SAVE_HISTORY,
                ScopeInterface::SCOPE_STORE,
                $category->getStoreId()
            );
            $category->setData('save_rewrites_history', $saveRewritesHistory);
            $urlRewrites = array_merge(
                $this->categoryUrlGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
            //@TODO BUG fix generation of Product Url Rewrites for children
            $this->deleteCategoryRewritesForChildren($category);
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

    /**
     * Remove product urls from storage
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function processUrlRewriteRemoving(EventObserver $observer)
    {
        //@TODO BUG fix removing Product Url Rewrites for category and category children
        /** @var Category $category */
        $category = $observer->getEvent()->getCategory();
        $this->deleteCategoryRewritesForChildren($category);
    }

    /***
     * @param Category $category
     * @return void
     */
    protected function deleteCategoryRewritesForChildren(Category $category)
    {
        $categoryIds = $category->getAllChildren();
        if ($categoryIds) {
            $categoryIds = explode(',', $categoryIds);
            foreach ($categoryIds as $categoryId) {
                $this->urlPersist->deleteByEntityData(
                    [
                        UrlRewrite::ENTITY_ID => $categoryId,
                        UrlRewrite::ENTITY_TYPE => CategoryUrlGenerator::ENTITY_TYPE_CATEGORY,
                    ]
                );
            }
        }
    }
}
