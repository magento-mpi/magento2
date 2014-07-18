<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestCase\OnePageCheckoutTest\Test;

use Magento\Checkout\Test\Constraint\AssertOrderTotalOnReviewPage;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mtf\TestCase\Step\StepInterface;

/**
 * Class PlaceOrder
 * Place order in one page checkout
 */
class PlaceOrder implements StepInterface
{
    /**
     * Onepage checkout page
     *
     * @var CheckoutOnepage
     */
    protected $checkoutOnepage;

    /**
     * Assert that Order Grand Total is correct on checkout page review block
     *
     * @var AssertOrderTotalOnReviewPage
     */
    protected $assertOrderTotalOnReviewPage;

    /**
     * One page checkout success page
     *
     * @var CheckoutOnepageSuccess
     */
    protected $checkoutOnepageSuccess;

    /**
     * Grand total price
     *
     * @var string
     */
    protected $grandTotal;

    /**
     * Checkout method
     *
     * @var string
     */
    protected $checkoutMethod;

    /**
     * Preparing step properties
     *
     * @construct
     * @param CheckoutOnepage $checkoutOnepage
     * @param AssertOrderTotalOnReviewPage $assertOrderTotalOnReviewPage
     * @param CheckoutOnepageSuccess $checkoutOnepageSuccess
     * @param string $checkoutMethod
     * @param string $grandTotal
     */
    public function __construct(
        CheckoutOnepage $checkoutOnepage,
        AssertOrderTotalOnReviewPage $assertOrderTotalOnReviewPage,
        CheckoutOnepageSuccess $checkoutOnepageSuccess,
        $checkoutMethod,
        $grandTotal
    ) {
        $this->checkoutOnepage = $checkoutOnepage;
        $this->assertOrderTotalOnReviewPage = $assertOrderTotalOnReviewPage;
        $this->grandTotal = $grandTotal;
        $this->checkoutOnepageSuccess = $checkoutOnepageSuccess;
        $this->checkoutMethod = $checkoutMethod;
    }

    /**
     * Run step that placing order
     *
     * @return array
     */
    public function run()
    {
        $this->assertOrderTotalOnReviewPage->configure(
            [
                'checkoutOnepage' => $this->checkoutOnepage,
                'grandTotal' => $this->grandTotal,
                'checkoutMethod' => $this->checkoutMethod
            ]
        );
        \PHPUnit_Framework_Assert::assertThat('PlaceOrderStep', $this->assertOrderTotalOnReviewPage);
        $this->checkoutOnepage->getReviewBlock()->placeOrder();
        $orderId = $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId();

        return ['orderId' => $orderId];
    }
}
