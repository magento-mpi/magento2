<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Model\Product;

use Magento\Catalog\Model\Product;
use Magento\CatalogUrlRewrite\Model\ProductUrlRewriteGenerator;
use Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator;
use Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder;

class CanonicalUrlRewriteGenerator
{
    /** @var \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator */
    protected $productUrlPathGenerator;

    /** @var \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder */
    protected $urlRewriteBuilder;

    /**
     * @param \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $productUrlPathGenerator
     * @param \Magento\UrlRewrite\Service\V1\Data\UrlRewriteBuilder $urlRewriteBuilder
     */
    public function __construct(
        ProductUrlPathGenerator $productUrlPathGenerator,
        UrlRewriteBuilder $urlRewriteBuilder
    ) {
        $this->productUrlPathGenerator = $productUrlPathGenerator;
        $this->urlRewriteBuilder = $urlRewriteBuilder;
    }

    /**
     * Generate list based on store view
     *
     * @param int $storeId
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($storeId, Product $product)
    {
        return [
            $this->urlRewriteBuilder->setEntityType(ProductUrlRewriteGenerator::ENTITY_TYPE)
                ->setEntityId($product->getId())->setStoreId($storeId)
                ->setRequestPath($this->productUrlPathGenerator->getUrlPathWithSuffix($product, $storeId))
                ->setTargetPath($this->productUrlPathGenerator->getCanonicalUrlPath($product))->create()
        ];
    }
}
