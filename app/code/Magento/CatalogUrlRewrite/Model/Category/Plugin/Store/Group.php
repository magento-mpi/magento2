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
use Magento\CatalogUrlRewrite\Model\Category\UrlGenerator as CategoryUrlGenerator;
use Magento\Store\Model\StoreManagerInterface;

class Group
{
    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var CategoryFactory */
    protected $categoryFactory;

    /** @var CategoryUrlGenerator */
    protected $categoryUrlGenerator;

    /** @var StoreManagerInterface */
    protected $storeManager;

    public function __construct(
        UrlPersistInterface $urlPersist,
        CategoryFactory $categoryFactory,
        CategoryUrlGenerator $categoryUrlGenerator,
        StoreManagerInterface $storeManager
    ) {
        $this->urlPersist = $urlPersist;
        $this->categoryFactory = $categoryFactory;
        $this->categoryUrlGenerator = $categoryUrlGenerator;
        $this->storeManager = $storeManager;
    }

    /**
     * @param \Magento\Store\Model\Group $group
     * @param \Magento\Store\Model\Group $result
     * @return \Magento\Store\Model\Group
     */
    public function afterSave(\Magento\Store\Model\Group $group, \Magento\Store\Model\Group $result)
    {
        if (!$group->isObjectNew() && $group->dataHasChangedFor('root_category_id')) {
            $this->storeManager->reinitStores();
            $rootCategoryId = $group->getRootCategoryId();
            $categories = $this->categoryFactory->create()
                ->load($rootCategoryId)
                ->getChildrenCategories();
            foreach ($group->getStoreIds() as $storeId) {
                $this->urlPersist->deleteByEntityData([UrlRewrite::STORE_ID => $storeId]);

                foreach ($categories as $category) {
                    /** @var \Magento\Catalog\Model\Category $category */
                    $category->setStoreId($storeId);
                    $urls = $this->categoryUrlGenerator->generate($category);
                    if ($urls) {
                        $this->urlPersist->save($urls);
                        $this->generateProductUrlRewrites($category);
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Generate url rewrites for products assigned to category
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return array
     */
    protected function generateProductUrlRewrites(\Magento\Catalog\Model\Category $category)
    {
        $collection = $category->getProductCollection()
            ->addAttributeToSelect('url_key')
            ->addAttributeToSelect('url_path');
        $productUrls = [];
        foreach ($collection as $product) {
            $product->setUrlPath($this->categoryUrlPathGenerator->generateUrlKey($product));
            $product->setStoreId($category->getStoreId());
            $product->setStoreIds($category->getStoreIds());
            $product->setData('save_rewrites_history', $category->getData('save_rewrites_history'));
            $productUrls = array_merge($productUrls, $this->productUrlGenerator->generate($product));
        }

        if ($category->hasChildren()) {
            foreach ($category->getChildrenCategories() as $subCategory) {
                $productUrls = array_merge(
                    $productUrls,
                    $this->generateProductUrlRewrites($subCategory)
                );
            }
        }
        return $productUrls;
    }
}
