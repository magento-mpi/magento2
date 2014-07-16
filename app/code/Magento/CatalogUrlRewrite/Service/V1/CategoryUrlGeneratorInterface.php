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
interface CategoryUrlGeneratorInterface
{
    /**
     * Generate list of urls
     * TODO: fix service parameter (MAGETWO-26225)
     *
     * @param \Magento\Catalog\Model\Category $category
     * @return \Magento\UrlRedirect\Service\V1\Data\UrlRewrite[]
     */
    public function generate($category);
}
