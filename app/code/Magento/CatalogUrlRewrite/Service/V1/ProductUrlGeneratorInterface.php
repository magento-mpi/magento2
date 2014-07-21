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
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function generate($product);

    /**
     * @param \Magento\Catalog\Model\Product $product
     * @param \Magento\Catalog\Model\Category[] $changedCategories
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function generateWithChangedCategories($product, $changedCategories);
}
