<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin;

use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\Catalog\Model\CategoryFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;

class Category
{
    /** @var ProductFactory */
    protected $productFactory;

    /** @var CategoryFactory */
    protected $categoryFactory;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param ProductFactory $productFactory
     * @param CategoryFactory $categoryFactory
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\CatalogUrlRewrite\Model\Product\UrlGenerator $productUrlGenerator
     */
    public function __construct(
        ProductFactory $productFactory,
        CategoryFactory $categoryFactory,
        UrlPersistInterface $urlPersist,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->productFactory = $productFactory;
        $this->categoryFactory = $categoryFactory;
        $this->urlPersist = $urlPersist;
        $this->productUrlGenerator = $productUrlGenerator;
    }

    /**
     * @param \Magento\Catalog\Model\Category $category
     * @param \Magento\Catalog\Model\Category $result
     * @return \Magento\Catalog\Model\Category
     */
    public function afterSave(\Magento\Catalog\Model\Category $category, \Magento\Catalog\Model\Category $result)
    {
        if ($category->getAffectedProductIds() !== array()) {
            foreach ($category->getAffectedProductIds() as $productId) {
                $this->clearProductUrls($productId);
                $this->generateProductUrls($productId);
            }
        }

        return $result;
    }

    /**
     * @param $productId
     * @return void
     */
    protected function clearProductUrls($productId)
    {
        $this->urlPersist->delete(
            [
                UrlRewrite::ENTITY_ID => $productId,
                UrlRewrite::ENTITY_TYPE => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
            ]
        );
    }

    /**
     * @param int $productId
     * @return void
     */
    protected function generateProductUrls($productId)
    {
        $productUrls = [];
        $product = $this->productFactory->create()->load($productId);
        if ($product->getCategoryIds() === []) {
            $productUrls = $this->productUrlGenerator->generate($product);
        } else {
            foreach ($product->getCategoryIds() as $categoryId) {
                $category = $this->categoryFactory->create()->load($categoryId);
                $product->setStoreId($category->getStoreId());
                $product->setStoreIds($category->getStoreIds());
                $productUrls = $this->productUrlGenerator->generate($product);
            }
        }
        if ($productUrls) {
            $this->urlPersist->replace($productUrls);
        }
    }
}
