<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Rating\Test\TestCase;

use Magento\Rating\Test\Page\Adminhtml\RatingIndex;
use Magento\Rating\Test\Page\Adminhtml\RatingNew;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Rating\Test\Fixture\Rating;
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
 * @ZephyrId MTA-144
 */
class CreateBackendProductRatingTest extends Injectable
{
    /**
     * Run create backend Product Rating test
     *
     * @param RatingIndex $ratingIndex
     * @param RatingNew $ratingNew
     * @param CatalogProductSimple $product
     * @param Rating $productRating
     * @return void
     */
    public function testCreateBackendProductRating(
        RatingIndex $ratingIndex,
        RatingNew $ratingNew,
        CatalogProductSimple $product,
        Rating $productRating
    ) {
        // Preconditions
        $product->persist();

        // Steps
        $ratingIndex->open();
        $ratingIndex->getGridPageActions()->addNew();
        $ratingNew->getRatingForm()->fill($productRating);
        $ratingNew->getPageActions()->save();
    }
}
