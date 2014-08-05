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
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Catalog\Block\Adminhtml\Form\Renderer\Attribute\Urlkey;

class Observer
{
    /** @var CategoryUrlRewriteGenerator */
    protected $categoryUrlRewriteGenerator;

    /** @var ProductUrlRewriteGenerator */
    protected $productUrlRewriteGenerator;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /**
     * @param CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     * @param UrlPersistInterface $urlPersist
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        CategoryUrlRewriteGenerator $categoryUrlRewriteGenerator,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator,
        UrlPersistInterface $urlPersist,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->categoryUrlRewriteGenerator = $categoryUrlRewriteGenerator;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
        $this->urlPersist = $urlPersist;
        $this->scopeConfig = $scopeConfig;
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
                $this->categoryUrlRewriteGenerator->generate($category),
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
                $this->categoryUrlRewriteGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
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
        $saveRewriteHistory = $category->getData('save_rewrites_history');
        $storeId = $category->getStoreId();
        $productUrls = $this->getCategoryProductsUrlRewrites($category, $storeId, $saveRewriteHistory);
        foreach ($category->getChildrenCategories() as $childCategory) {
            $productUrls = array_merge($productUrls, $this->getCategoryProductsUrlRewrites($childCategory, $storeId,
                $saveRewriteHistory));
        }
        return $productUrls;
    }

    /**
     * @param Category $category
     * @param int $storeId
     * @param bool $saveRewriteHistory
     * @return array
     */
    protected function getCategoryProductsUrlRewrites(Category $category, $storeId, $saveRewriteHistory)
    {
        $categories = $category->getProductCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $productUrls = [];
        foreach ($categories as $product) {
            $product->setStoreId($storeId);
            $product->setData('save_rewrites_history', $saveRewriteHistory);
            $productUrls = array_merge($productUrls, $this->productUrlRewriteGenerator->generate($product));
        }
        return $productUrls;
    }

    /**
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
                        UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE_CATEGORY,
                    ]
                );
            }
        }
    }
}
