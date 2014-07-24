<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Review\Test\Constraint;

use Magento\Review\Test\Page\Adminhtml\ReviewIndex;
use Magento\Review\Test\Page\Adminhtml\ReviewEdit;
use Magento\Review\Test\Fixture\ReviewInjectable;
use Mtf\Constraint\AbstractAssertForm;

/**
 * Class AssertProductRatingOnReviewPage
 */
class AssertProductRatingOnReviewPage extends AbstractAssertForm
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that product rating is displayed on product review(backend)
     *
     * @param ReviewIndex $reviewIndex
     * @param ReviewEdit $reviewEdit
     * @param ReviewInjectable $review
     * @param ReviewInjectable|null $reviewInitial [optional]
     * @return void
     */
    public function processAssert(
        ReviewIndex $reviewIndex,
        ReviewEdit $reviewEdit,
        ReviewInjectable $review,
        ReviewInjectable $reviewInitial = null
    ) {
        $filter = ['title' => $review->getTitle()];

        $reviewIndex->open();
        $reviewIndex->getReviewGrid()->searchAndOpen($filter);

        $ratingReview = array_replace(
            ($reviewInitial && $reviewInitial->hasData('ratings')) ? $reviewInitial->getRatings() : [],
            $review->hasData('ratings') ? $review->getRatings() : []
        );
        $ratingReview = $this->sortDataByPath($ratingReview, '::title');
        $ratingForm = $reviewEdit->getReviewForm()->getRatings();
        $ratingForm = $this->sortDataByPath($ratingForm, '::title');
        $error = $this->verifyData($ratingReview, $ratingForm);
        \PHPUnit_Framework_Assert::assertTrue(empty($error), $error);
    }

    /**
     * Text success product rating is displayed on edit review page(backend)
     *
     * @return string
     */
    public function toString()
    {
        return 'Product rating is displayed on edit review page(backend).';
    }
}
