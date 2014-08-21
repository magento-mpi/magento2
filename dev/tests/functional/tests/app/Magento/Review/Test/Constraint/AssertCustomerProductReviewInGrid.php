<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Reports\Test\Page\Adminhtml\CustomerReportReview;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;

/**
 * Class AssertProductReviewInGrid
 * Check that Customer review is displayed in grid
 */
class AssertCustomerProductReviewInGrid extends AssertProductReviewInGrid
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that Customer review is displayed in grid
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewInjectable $review
     * @param CustomerReportReview $customerReportReview
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @param string $gridStatus
     * @param ReviewInjectable $reviewInitial
     * @return void
     */
    public function processAssert(
        ReviewIndex $reviewIndex,
        ReviewInjectable $review,
        CustomerReportReview $customerReportReview,
        CustomerInjectable $customer,
        CatalogProductSimple $product = null,
        $gridStatus = '',
        ReviewInjectable $reviewInitial = null
    ) {
        if ($product === null) {
            $product = $reviewInitial === null
                ? $review->getDataFieldConfig('entity_id')['source']->getEntity()
                : $reviewInitial->getDataFieldConfig('entity_id')['source']->getEntity();
        }
        $filter = $this->prepareFilter($product, $review, $gridStatus);

        $customerReportReview->open();
        $customerReportReview->getGridBlock()->openReview($customer);
        $reviewIndex->getReviewGrid()->search($filter);
        unset($filter['visible_in']);
        \PHPUnit_Framework_Assert::assertTrue(
            $reviewIndex->getReviewGrid()->isRowVisible($filter, false),
            'Customer review is absent in Review grid.'
        );
    }

    /**
     * Text success exist review in grid on product reviews tab
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer review is present in grid on product reviews tab.';
    }
}
