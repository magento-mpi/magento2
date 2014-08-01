<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Store;

use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Catalog\Model\ProductFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;
use Magento\Store\Model\Store;

class Group
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

    /** @var StoreManagerInterface  */
    protected $storeManager;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param CategoryFactory $categoryFactory
     * @param ProductFactory $productFactory
     * @param CategoryUrlGenerator $categoryUrlGenerator
     * @param ProductUrlGenerator $productUrlGenerator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        CategoryFactory $categoryFactory,
        ProductFactory $productFactory,
        CategoryUrlGenerator $categoryUrlGenerator,
        ProductUrlGenerator $productUrlGenerator,
        StoreManagerInterface $storeManager
    ) {
        $this->urlPersist = $urlPersist;
        $this->categoryFactory = $categoryFactory;
        $this->productFactory = $productFactory;
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->productUrlGenerator = $productUrlGenerator;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Store\Model\Resource\Group $object
     * @param callable $proceed
     * @param \Magento\Store\Model\Group $group
     * @return \Magento\Store\Model\Resource\Group
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Store\Model\Resource\Group $object,
        \Closure $proceed,
        \Magento\Store\Model\Group $group
    ) {
        $originGroup = $group;
        $result = $proceed($originGroup);
        if (!$group->isObjectNew()
            && ($group->dataHasChangedFor('website_id')
                || $group->dataHasChangedFor('root_category_id'))
        ) {
            $this->storeManager->reinitStores();
            foreach ($group->getStoreIds() as $storeId) {
                $this->urlPersist->deleteByEntityData([UrlRewrite::STORE_ID => $storeId]);
            }

            $this->urlPersist->replace(
                $this->generateCategoryUrls($group->getRootCategoryId(), $group->getStoreIds())
            );

            $this->urlPersist->replace(
                $this->generateProductUrls($group->getWebsiteId(), $group->getOrigData('website_id'))
            );
        }

        return $result;
    }

    /**
     * Generate url rewrites for products assigned to website
     *
     * @param int $websiteId
     * @param int $originWebsiteId
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
            ->addAttributeToSelect(array('name', 'url_path', 'url_key'))
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
     * @param int $rootCategoryId
     * @param array $storeIds
     * @return array
     */
    protected function generateCategoryUrls($rootCategoryId, $storeIds)
    {
        $urls = [];
        $categories = $this->categoryFactory->create()->getCategories($rootCategoryId, 1, false, true);
        foreach ($categories as $category) {
            /** @var \Magento\Catalog\Model\Category $category */
            $category->setStoreId(Store::DEFAULT_STORE_ID);
            $category->setStoreIds($storeIds);
            $urls = array_merge(
                $urls,
                $this->categoryUrlGenerator->generate($category)
            );
        }
        return $urls;
    }
}
