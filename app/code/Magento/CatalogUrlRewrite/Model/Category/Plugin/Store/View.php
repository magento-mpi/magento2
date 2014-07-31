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
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;
use Magento\Store\Model\Store;

class View
{
    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var CategoryFactory */
    protected $categoryFactory;

    /** @var ProductFactory */
    protected $productFactory;

    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productFactory
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        CategoryFactory $categoryFactory,
        ProductFactory $productFactory,
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->urlPersist = $urlPersist;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
    }

    /**
     * @param \Magento\Store\Model\Resource\Store $object
     * @param callable $proceed
     * @param Store $store
     *
     * @return \Magento\Store\Model\Resource\Store
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Store\Model\Resource\Store $object,
        \Closure $proceed,
        \Magento\Store\Model\Store $store
    ) {
        $originStore = $store;
        $result = $proceed($originStore);
        if ($store->isObjectNew() || $store->dataHasChangedFor('group_id')) {
            if (!$store->isObjectNew()) {
                $this->urlPersist->deleteByEntityData([UrlRewrite::STORE_ID => $store->getId()]);
            }

            $this->urlPersist->replace(
                $this->generateCategoryUrls($store->getRootCategoryId(), $store->getId())
            );

            $this->urlPersist->replace(
                $this->generateProductUrls($store->getWebsiteId(), $store->getOrigData('website_id'))
            );
        }

        return $result;
    }

    /**
     * Generate url rewrites for products assigned to website
     *
     * @param $websiteId
     * @param $originWebsiteId
     * @return array
     */
    protected function generateProductUrls($websiteId, $originWebsiteId)
    {
        $urls = [];
        $websiteIds = $websiteId != $originWebsiteId
            ? [$websiteId, $originWebsiteId]
            : [$websiteId];
        $collection = $this->productFactory->create()
            ->getCollection()
            ->addCategoryIds()
            ->addAttributeToSelect(array('name', 'url_path'))
            ->addWebsiteFilter($websiteIds);
        foreach ($collection as $product) {
            /** @var \Magento\Catalog\Model\Product $product */
            $product->setStoreId(Store::DEFAULT_STORE_ID);
            $urls = array_merge(
                $urls,
                $this->productUrlGenerator->generate($product)
            );
        }
        return $urls;
    }

    /**
     * @param Store $store
     * @return array
     */
    protected function generateCategoryUrls($rootCategoryId, $storeId)
    {
        $urls = [];
        $categories = $this->categoryFactory->create()
            ->load($rootCategoryId)
            ->getAllChildren(true);
        $categories = array_diff($categories, [$rootCategoryId]);
        foreach ($categories as $categoryId) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category = $this->categoryFactory->create()->load($categoryId);
            $category->setStoreId($storeId);
            $urls = array_merge(
                $urls,
                $this->categoryUrlGenerator->generate($category)
            );
        }
        return $urls;
    }
}
