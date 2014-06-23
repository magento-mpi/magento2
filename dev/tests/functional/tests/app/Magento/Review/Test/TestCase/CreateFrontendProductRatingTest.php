<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\RatingEdit;
use Magento\Review\Test\Page\Adminhtml\RatingIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Frontend Product Rating
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
class CreateFrontendProductRatingTest extends Injectable
{
    /**
     * @var CatalogProductView
     */
    protected $catalogProductView;

    /**
     * @var RatingIndex
     */
    protected $ratingIndex;

    /**
     * @var RatingEdit
     */
    protected $ratingEdit;

    /**
     * @var ReviewInjectable
     */
    protected $review;

    /**
     * Prepare data
     *
     * @param CatalogProductSimple $product
     * @return array
     */
    public function __prepare(CatalogProductSimple $product)
    {
        $product->persist();
        return ['product' => $product];
    }

    /**
     * Injection data
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
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $review
     * @return void
     */
    public function test(
        CatalogProductSimple $product,
        ReviewInjectable $review
    ) {
        // Steps
        $this->catalogProductView->init($product);
        $this->catalogProductView->open();
        $this->catalogProductView->getReviewSummaryBlock()->getAddReviewLink()->click();

        $this->catalogProductView->getReviewFormBlock()->fill($review);
        $this->catalogProductView->getReviewFormBlock()->submit();

        // Prepare for tear down
        $this->review = $review;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        if (!$this->review) {
            return;
        }

        foreach ($this->review->getRatings() as $rating) {
            $filter = ['rating_code' => $rating['title']];
            $this->ratingIndex->open();
            $this->ratingIndex->getRatingGrid()->searchAndOpen($filter);
            $this->ratingEdit->getPageActions()->delete();
        }
    }
}
