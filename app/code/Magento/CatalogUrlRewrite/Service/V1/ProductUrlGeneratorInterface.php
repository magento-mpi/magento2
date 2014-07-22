<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\CatalogUrlRewrite\Service\V1;

/**
 * Product Generator
 */
interface ProductUrlGeneratorInterface
{
    /**
     * Generate list of urls
     * TODO: fix service parameter (@TODO: UrlRewrite)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generate($product);

    /**
     * TODO: hack for obtaining data from changed categories. Replace on Service Data Object (@TODO: UrlRewrite)
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category[] $changedCategories
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite[]
     */
    public function generateWithChangedCategories($product, $changedCategories);
}
