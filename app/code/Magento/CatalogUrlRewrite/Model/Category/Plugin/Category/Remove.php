<?php
/**
 * {license_notice}
 *   
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Category;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\UrlRewrite\Model\UrlPersistInterface;
use Magento\Catalog\Model\Category;
use Magento\CatalogUrlRewrite\Model\CategoryUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\Catalog\Model\CategoryFactory;
use Magento\Store\Model\Store;

class Remove
{
    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var  CategoryFactory */
    protected $categoryFactory;

    /** @var ProductUrlRewriteGenerator */
    protected $productUrlRewriteGenerator;

    /**
     * @param UrlPersistInterface $urlPersist
     * @param CategoryFactory $categoryFactory
     * @param ProductUrlRewriteGenerator $productUrlRewriteGenerator
     */
    public function __construct(
        UrlPersistInterface $urlPersist,
        CategoryFactory $categoryFactory,
        ProductUrlRewriteGenerator $productUrlRewriteGenerator
    ) {
        $this->urlPersist = $urlPersist;
        $this->categoryFactory = $categoryFactory;
        $this->productUrlRewriteGenerator = $productUrlRewriteGenerator;
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
        $this->urlPersist->deleteByData([
            UrlRewrite::ENTITY_ID => $categoryId,
            UrlRewrite::ENTITY_TYPE => CategoryUrlRewriteGenerator::ENTITY_TYPE,
        ]);
        $collection = $this->categoryFactory->create()
            ->load($categoryId)
            ->getProductCollection()
            ->addAttributeToSelect(['url_key', 'url_path', 'name']);
        $productUrls = [];
        foreach ($collection as $product) {
            $product->setStoreId(Store::DEFAULT_STORE_ID);
            $productUrls = array_merge($productUrls, $this->productUrlRewriteGenerator->generate($product));
        }
        $this->urlPersist->replace($productUrls);
    }
}
