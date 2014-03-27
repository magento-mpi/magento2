<?php
/**
 * Default implementation of product review service provider
 *
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Catalog\Block\Product\ReviewRenderer;

use Magento\Catalog\Block\Product\ReviewRendererInterface;

class DefaultProvider implements ReviewRendererInterface
{
    /**
     * Get product review summary html
     *
     * @param \Magento\Catalog\Model\Product $product
     * @param string $templateType
     * @param bool $displayIfNoReviews
     * @return string
     */
    public function getReviewsSummaryHtml(
        \Magento\Catalog\Model\Product $product,
        $templateType = self::DEFAULT_REVIEW,
        $displayIfNoReviews = false
    ) {
        return '';
    }
}
