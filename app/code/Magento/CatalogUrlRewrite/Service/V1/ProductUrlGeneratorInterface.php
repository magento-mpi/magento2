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
     * @param int $productId
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function generate($productId);
}
