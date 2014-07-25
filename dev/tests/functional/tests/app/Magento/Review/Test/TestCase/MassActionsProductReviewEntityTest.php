<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Mtf\TestCase\Injectable;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Test creation for MassActions ProductReviewEntity
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Simple product created
 * 2. Product Review created on frontend
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate to Marketing -> User Content -> Reviews
 * 3. Search and select review created in precondition
 * 4. Select Mass Action
 * 5. Select Action from Dataset
 * 6. Click "Submit" button
 * 7. Perform Asserts
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MAGETWO-26618
 */
class MassActionsProductReviewEntityTest extends Injectable
{
    /**
     * Review index page
     *
     * @var ReviewIndex
     */
    protected $reviewIndex;

    /**
     * Prepare data
     *
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $review
     * @return array
     */
    public function __prepare(CatalogProductSimple $product, ReviewInjectable $review)
    {
        $product->persist();
        $review->persist();

        return ['product' => $product, 'review' => $review];
    }

    /**
     * Injection data
     *
     * @param ReviewIndex $reviewIndex
     * @return void
     */
    public function __inject(ReviewIndex $reviewIndex)
    {
        $this->reviewIndex = $reviewIndex;
    }

    /**
     * Apply for MassActions ProductReviewEntity
     *
     * @param string $gridActions
     * @param string $gridStatus
     * @param ReviewInjectable $review
     * @return void
     */
    public function test($gridActions, $gridStatus, ReviewInjectable $review)
    {
        // Steps
        $this->reviewIndex->open();
        $this->reviewIndex->getReviewGrid()->massaction(
            [['title' => $review->getTitle()]],
            [$gridActions => $gridStatus],
            ($gridActions == 'Delete' ? true : false)
        );
    }
}
