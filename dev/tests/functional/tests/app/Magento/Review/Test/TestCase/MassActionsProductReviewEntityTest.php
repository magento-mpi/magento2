<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Review\Test\Page\Adminhtml\RatingIndex;
use Magento\Review\Test\Page\Adminhtml\RatingEdit;
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
     * Backend rating grid page
     *
     * @var RatingIndex
     */
    protected $ratingIndex;

    /**
     * Backend rating edit page
     *
     * @var RatingEdit
     */
    protected $ratingEdit;

    /**
     * Review index page
     *
     * @var ReviewIndex
     */
    protected $reviewIndex;

    /**
     * Fixture review
     *
     * @var ReviewInjectable
     */
    protected $review;

    /**
     * Injection data
     *
     * @param ReviewIndex $reviewIndex
     * @param RatingIndex $ratingIndex
     * @param RatingEdit $ratingEdit
     * @param ReviewInjectable $review
     * @return array
     */
    public function __inject(
        ReviewIndex $reviewIndex,
        RatingIndex $ratingIndex,
        RatingEdit $ratingEdit,
        ReviewInjectable $review
    ) {
        $this->reviewIndex = $reviewIndex;
        $this->ratingIndex = $ratingIndex;
        $this->ratingEdit = $ratingEdit;
        $this->review = $review;
        $this->review->persist();

        return ['review' => $this->review];
    }

    /**
     * Apply for MassActions ProductReviewEntity
     *
     * @param string $gridActions
     * @param string $gridStatus
     * @return void
     */
    public function test($gridActions, $gridStatus)
    {
        // Steps
        $this->reviewIndex->open();
        $this->reviewIndex->getReviewGrid()->massaction(
            [['title' => $this->review->getTitle()]],
            [$gridActions => $gridStatus],
            ($gridActions == 'Delete' ? true : false)
        );
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $this->ratingIndex->open();
        $ratingGrid = $this->ratingIndex->getRatingGrid();
        $pageActions = $this->ratingEdit->getPageActions();
        foreach ($this->review->getRatings() as $rating) {
            $ratingGrid->searchAndOpen(['rating_code' => $rating['title']]);
            $pageActions->delete();
        }
    }
}
