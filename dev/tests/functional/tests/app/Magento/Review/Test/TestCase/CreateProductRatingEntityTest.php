<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Review\Test\Page\Adminhtml\RatingIndex;
use Magento\Review\Test\Page\Adminhtml\RatingNew;
use Magento\Review\Test\Page\Adminhtml\RatingEdit;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Review\Test\Fixture\Rating;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create Backend Product Rating
 *
 * Test Flow:
 *
 * Preconditions:
 * 1. Create simple product
 *
 * Steps:
 * 1. Login to backend
 * 2. Navigate Stores->Attributes->Rating
 * 3. Add New Rating
 * 4. Fill data according to dataset
 * 5. Save Rating
 * 6. Perform asserts
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MAGETWO-23331
 */
class CreateProductRatingEntityTest extends Injectable
{
    /**
     * @var Rating
     */
    protected $productRating;

    /**
     * @var RatingIndex
     */
    protected $ratingIndex;

    /**
     * @var RatingNew
     */
    protected $ratingNew;

    /**
     * @var RatingEdit
     */
    protected $ratingEdit;

    /**
     * Injection data
     *
     * @param RatingIndex $ratingIndex
     * @param RatingNew $ratingNew
     * @param RatingEdit $ratingEdit
     * @return void
     */
    public function __inject(
        RatingIndex $ratingIndex,
        RatingNew $ratingNew,
        RatingEdit $ratingEdit
    ) {
        $this->ratingIndex = $ratingIndex;
        $this->ratingNew = $ratingNew;
        $this->ratingEdit = $ratingEdit;
    }

    /**
     * Run create backend Product Rating test
     *
     * @param CatalogProductSimple $product
     * @param Rating $productRating
     * @return void
     */
    public function testCreateProductRatingEntityTest(
        CatalogProductSimple $product,
        Rating $productRating
    ) {
        // Preconditions
        $product->persist();

        // Steps
        $this->ratingIndex->open();
        $this->ratingIndex->getGridPageActions()->addNew();
        $this->ratingNew->getRatingForm()->fill($productRating);
        $this->ratingNew->getPageActions()->save();

        // Prepare data for tear down
        $this->productRating = $productRating;
    }

    /**
     * Clear data after test
     *
     * @return void
     */
    public function tearDown()
    {
        $filter = ['rating_code' => $this->productRating->getRatingCode()];
        $this->ratingIndex->open();
        $this->ratingIndex->getRatingGrid()->searchAndOpen($filter);
        $this->ratingEdit->getPageActions()->delete();
    }
}
