<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Store;

use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\CatalogUrlRewrite\Model\Category\CategoryUrlPathGenerator;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;

class View
{
    /** @var CategoryUrlPathGenerator */
    protected $categoryUrlPathGenerator;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var CategoryFactory */
    protected $categoryFactory;

    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param CategoryUrlPathGenerator $categoryUrlPathGenerator
     * @param UrlPersistInterface $urlPersist
     * @param CategoryFactory $categoryFactory
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     */
    public function __construct(
        CategoryUrlPathGenerator $categoryUrlPathGenerator,
        UrlPersistInterface $urlPersist,
        CategoryFactory $categoryFactory,
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->categoryUrlPathGenerator = $categoryUrlPathGenerator;
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlPersist = $urlPersist;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param \Magento\Store\Model\Store $store
     * @param \Magento\Store\Model\Store $result
     * @return \Magento\Store\Model\Store
     */
    public function afterSave(\Magento\Store\Model\Store $store, \Magento\Store\Model\Store $result)
    {
        if ($store->isObjectNew() || $store->dataHasChangedFor('group_id')) {
            if (!$store->isObjectNew()) {
                $this->urlPersist->deleteByEntityData([UrlRewrite::STORE_ID => $store->getId()]);
            }

            $categories = $this->categoryFactory->create()
                ->load($store->getRootCategoryId())
                ->getChildrenCategories();
            foreach ($categories as $category) {
                /** @var \Magento\Catalog\Model\Category $category */
                $category->setStoreId($store->getId());
                $urls = $this->categoryUrlGenerator->generate($category);
                if ($urls) {
                    $this->urlPersist->replace(array_merge(
                        $urls,
                        $this->generateProductUrlRewrites($category)
                    ));
                }
            }
        }

        return $result;
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
            $productUrls = array_merge($productUrls, $this->productUrlGenerator->generate($product));
        }

        foreach ($category->getChildrenCategories() as $subCategory) {
            $productUrls = array_merge(
                $productUrls,
                $this->generateProductUrlRewrites($subCategory)
            );
        }

        return $productUrls;
    }
}
