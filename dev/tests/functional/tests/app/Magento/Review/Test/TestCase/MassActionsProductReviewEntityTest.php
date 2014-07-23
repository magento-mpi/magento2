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
 * 1. login to backend
 * 2. navigate to Marketing -> User Content -> Reviews
 * 3. search and select review created in precondition
 * 4. select Mass Action
 * 5. select Action from Dataset
 * 6. click "Submit" button
 * 7. perform Asserts
 *
 * @group Reviews_and_Ratings_(MX)
 * @ZephyrId MAGETWO-26618
 */
class MassActionsProductReviewEntityTest extends Injectable
{
    /**
     * Review fixture
     *
     * @var ReviewInjectable
     */
    protected $review;

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
        $this->review = $review;

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
     * Creation for MassActions ProductReviewEntity
     *
     * @param string $gridActions
     * @param string $gridStatus
     * @return void
     */
    public function test($gridActions, $gridStatus)
    {
        $this->reviewIndex->open();
        $this->reviewIndex->getReviewGrid()->actions(
            $gridActions,
            [['title' => $this->review->getTitle()]],
            $gridStatus
        );
    }
}
