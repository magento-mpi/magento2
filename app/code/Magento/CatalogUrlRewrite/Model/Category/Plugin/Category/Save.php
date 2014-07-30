<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Category\Plugin\Category;

use Magento\UrlRewrite\Service\V1\UrlPersistInterface;
use Magento\Catalog\Model\ProductFactory;
use Magento\UrlRewrite\Service\V1\Data\UrlRewrite;
use Magento\CatalogUrlRewrite\Model\Product\UrlGenerator as ProductUrlGenerator;

class Save
{
    /** @var ProductFactory */
    protected $productFactory;

    /** @var UrlPersistInterface */
    protected $urlPersist;

    /** @var ProductUrlGenerator */
    protected $productUrlGenerator;

    /**
     * @param ProductFactory $productFactory
     * @param UrlPersistInterface $urlPersist
     * @param \Magento\CatalogUrlRewrite\Model\Product\UrlGenerator $productUrlGenerator
     */
    public function __construct(
        ProductFactory $productFactory,
        UrlPersistInterface $urlPersist,
        ProductUrlGenerator $productUrlGenerator
    ) {
        $this->productFactory = $productFactory;
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
        $productIds = $category->getAffectedProductIds()?: [];
        foreach ($productIds as $productId) {
            $this->clearProductUrls($productId);
            $this->generateProductUrls($productId);
        }

        return $result;
    }

    /**
     * @param $productId
     * @return void
     */
    protected function clearProductUrls($productId)
    {
        $this->urlPersist->deleteByEntityData(
            [
                UrlRewrite::ENTITY_ID => $productId,
                UrlRewrite::ENTITY_TYPE => ProductUrlGenerator::ENTITY_TYPE_PRODUCT,
            ]
        );
    }

    /**
     * Generate rewrite urls for product
     *
     * @param int $productId
     * @return void
     */
    protected function generateProductUrls($productId)
    {
        $product = $this->productFactory->create()->load($productId);
        $productUrls = $this->productUrlGenerator->generate($product);
        if ($productUrls) {
            $this->urlPersist->replace($productUrls);
        }
    }
}
