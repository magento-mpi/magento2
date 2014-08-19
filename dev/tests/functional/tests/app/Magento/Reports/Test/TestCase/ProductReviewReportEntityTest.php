<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Review\Test\Fixture\ReviewInjectable;

/**
 * Test Creation for ProductReviewReportEntity
 *
 * Preconditions:
 * 1. Create simple product
 * 2. Create review for this product
 *
 * Test Flow:
 * 1. Login as admin
 * 2. Navigate to the Reports>Reviews>By Products
 * 3. Perform appropriate assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27223
 */
class ProductReviewReportEntityTest extends Injectable
{
    /**
     * Creation product review report entity
     *
     * @param ReviewInjectable $review
     * @return void
     */
    public function test(ReviewInjectable $review)
    {
        // Preconditions
        $review->persist();
    }
}
