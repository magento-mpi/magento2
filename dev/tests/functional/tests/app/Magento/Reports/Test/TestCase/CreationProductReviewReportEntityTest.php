<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\TestCase;

use Mtf\TestCase\Injectable;
use Mtf\Fixture\FixtureFactory;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Reports\Test\Page\Adminhtml\ProductReportReview;

/**
 * Test Creation ProductReviewReportEntity
 *
 * Preconditions:
 * 1. Create simple product
 * 2. Create review for this product
 *
 * Test Flow:
 * 1. Login as admin
 * 2. Navigate to the Reports>Reviews>By Products
 * 3. Open report for this product
 * 4. Perform appropriate assertions.
 *
 * @group Reports_(MX)
 * @ZephyrId MAGETWO-27223
 */
class CreationProductReviewReportEntityTest extends Injectable
{
    /**
     * Product reviews report page
     *
     * @var ProductReportReview
     */
    protected $productReportReview;

    /**
     * Prepare data
     *
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function __prepare(FixtureFactory $fixtureFactory)
    {
        $review = $fixtureFactory->createByCode('reviewInjectable', ['dataSet' => 'frontend_review']);
        $review->persist();

        return ['review' => $review];
    }

    /**
     * Inject pages
     *
     * @param ProductReportReview $productReportReview
     * @return void
     */
    public function __inject(ProductReportReview $productReportReview)
    {
        $this->productReportReview = $productReportReview;
    }

    /**
     * Creation product review report entity
     *
     * @param ReviewInjectable $review
     * @return void
     */
    public function test(ReviewInjectable $review)
    {
        // Steps
        $this->productReportReview->open();
        $this->productReportReview->getGridBlock()
            ->openReview($review->getDataFieldConfig('entity_id')['source']->getEntity()->getName());
    }
}
