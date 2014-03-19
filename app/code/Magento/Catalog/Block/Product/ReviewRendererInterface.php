<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Block\Product;

interface ReviewRendererInterface
{
    const SHORT_REVIEW   = 'short';
    const DEFAULT_REVIEW = 'default';

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
    );
}
