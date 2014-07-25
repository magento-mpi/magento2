<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category;

use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\Product\ProductUrlGenerator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\UrlSaveInterface;

class Observer
{
    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /** @var UrlSaveInterface */
    protected $urlSave;

    /** var \Magento\CatalogUrlRewrite\Helper\Data */
    protected $catalogUrlRewriteHelper;

    /**
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     * @param UrlSaveInterface $urlSave
     * @param \Magento\CatalogUrlRewrite\Helper\Data $catalogUrlRewriteHelper
     */
    public function __construct(
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator,
        UrlSaveInterface $urlSave,
        \Magento\CatalogUrlRewrite\Helper\Data $catalogUrlRewriteHelper
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlSave = $urlSave;
        $this->catalogUrlRewriteHelper = $catalogUrlRewriteHelper;// TODO: MAGETWO-26285
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
        if (!$category->getData('url_key') || $category->getOrigData('url_key') !== $category->getData('url_key')) {
            $urls = array_merge(
                $this->categoryUrlGenerator->generate($category),
                $this->generateProductUrlRewrites($category)
            );
        } elseif ($category->getOrigData('affected_product_ids') !== $category->getData('affected_product_ids')) {
            $urls = $this->generateProductUrlRewrites($category);
        }
        if ($urls) {
            $this->urlSave->save($urls);
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
        }
        return $productUrls;
    }
}
