<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\TestCase;

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewEdit;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Mtf\TestCase\Injectable;

/**
 * Test Creation for Create ProductReviewEntity Backend
 *
 * Test Flow:
 * Preconditions:
 * 1. Simple Product created
 *
 * Steps:
 * 1. login to backend
 * 2. navigate to Marketing -> User Content -> Reviews
 * 3. click the "+" (Add New Review) button
 * 4. select the product in the Products Grid
 * 5. fill data according to DataSet
 * 6. click "Save Review" button
 * 7. perform Asserts
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MAGETWO-26476
 */
class CreateProductReviewEntityBackendTest extends Injectable
{
    /**
     * ReviewIndex page
     *
     * @var ReviewIndex
     */
    protected $reviewIndex;

    /**
     * ReviewEdit page
     *
     * @var ReviewEdit
     */
    protected $reviewEdit;

    /**
     * Inject pages into test
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewEdit $reviewEdit
     * @return void
     */
    public function __inject(ReviewIndex $reviewIndex, ReviewEdit $reviewEdit)
    {
        $this->reviewIndex = $reviewIndex;
        $this->reviewEdit = $reviewEdit;
    }

    /**
     * Run Create Product Review Entity Backend Test
     *
     * @param CatalogProductSimple $product
     * @param ReviewInjectable $review
     * @return void
     */
    public function test(CatalogProductSimple $product, ReviewInjectable $review)
    {
        // Precondition:
        $product->persist();
        $filter = ['id' => $product->getId(), 'name' => $product->getName()];

        // Steps:
        $this->reviewIndex->open();
        $this->reviewIndex->getReviewActions()->addNew();
        $this->reviewEdit->getProductGrid()->searchAndOpen($filter);
        $this->reviewEdit->getReviewForm()->fill($review);
        $this->reviewEdit->getPageActions()->save();
    }
}
