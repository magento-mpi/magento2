<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Test\TestStep;

use Magento\Checkout\Test\Constraint\AssertOrderTotalOnReviewPage;
use Magento\Checkout\Test\Page\CheckoutOnepage;
use Magento\Checkout\Test\Page\CheckoutOnepageSuccess;
use Mtf\TestStep\TestStepInterface;

/**
 * Class PlaceOrderStep
 * Place order in one page checkout
 */
class PlaceOrderStep implements TestStepInterface
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
     * Place order after checking order totals on review step
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
        \PHPUnit_Framework_Assert::assertThat(get_class($this), $this->assertOrderTotalOnReviewPage);
        $this->checkoutOnepage->getReviewBlock()->placeOrder();
        $orderId = $this->checkoutOnepageSuccess->getSuccessBlock()->getGuestOrderId();

        return ['orderId' => $orderId];
    }
}
