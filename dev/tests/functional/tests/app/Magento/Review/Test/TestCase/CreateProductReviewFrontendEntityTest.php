<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\RatingEdit;
use Magento\Review\Test\Page\Adminhtml\RatingIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Frontend Product Review
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create simple product
 * 2. Create custom rating type
 *
 * Steps:
 * 1. Open frontend
 * 2. Go to product page
 * 3. Click "Be the first to review this product"
 * 4. Fill data according to dataset
 * 5. click "Submit review"
 * 6. Perform all assertions
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MAGETWO-25519
 */
class CreateProductReviewFrontendEntityTest extends Injectable
{
    /**
     * Frontend product view page
     *
     * @var CatalogProductView
     */
    protected $catalogProductView;

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
     * Fixture review
     *
     * @var ReviewInjectable
     */
    protected $review;

    /**
     * Injection data
     *
     * @param CatalogProductView $catalogProductView
     * @param RatingIndex $ratingIndex
     * @param RatingEdit $ratingEdit
     * @return void
     */
    public function __inject(
        CatalogProductView $catalogProductView,
        RatingIndex $ratingIndex,
        RatingEdit $ratingEdit
    ) {
        $this->catalogProductView = $catalogProductView;
        $this->ratingIndex = $ratingIndex;
        $this->ratingEdit = $ratingEdit;
    }

    /**
     * Run create frontend product rating test
     *
     * @param ReviewInjectable $review
     * @return void
     */
    public function test(
        ReviewInjectable $review
    ) {
        // Prepare for tear down
        $this->review = $review;

        // Steps
        $product = $review->getDataFieldConfig('entity_id')['source']->getEntity();
        $this->catalogProductView->init($product);
        $this->catalogProductView->open();
        $reviewLink = $this->catalogProductView->getReviewSummaryBlock()->getAddReviewLink();
        if ($reviewLink->isVisible()) {
            $reviewLink->click();
        }
        $reviewForm = $this->catalogProductView->getReviewFormBlock();
        $reviewForm->fill($review);
        $reviewForm->submit();
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
            $filter = ['rating_code' => $rating['title']];
            $ratingGrid->searchAndOpen($filter);
            $pageActions->delete();
        }
    }
}
