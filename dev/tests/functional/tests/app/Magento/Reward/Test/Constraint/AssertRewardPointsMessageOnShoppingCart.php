<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reward\Test\Constraint; 

use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Magento\Reward\Test\Page\CheckoutCart;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardPointsMessageOnShoppingCart
 * Assert that reward points message is displayed on shopping cart page
 */
class AssertRewardPointsMessageOnShoppingCart extends AbstractConstraint
{
    const CHECKOUT_REWARD_MESSAGE = 'Check out now and earn %d Reward points for this order.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that reward points message is displayed on shopping cart page
     *
     * @param CatalogProductSimple $product
     * @param CatalogProductView $productView
     * @param CheckoutCart $checkoutCart
     * @param string $checkoutReward
     * @return void
     */
    public function processAssert(
        CatalogProductSimple $product,
        CatalogProductView $productView,
        CheckoutCart $checkoutCart,
        $checkoutReward
    ) {
        $productView->init($product);
        $productView->open();
        $productView->getViewBlock()->clickAddToCartButton();
        $actualMessage = $checkoutCart->getCheckoutTooltipBlock()->getRewardMessages();
        $expectedMessage = sprintf(self::CHECKOUT_REWARD_MESSAGE, $checkoutReward);

        \PHPUnit_Framework_Assert::assertEquals(
            $expectedMessage,
            trim($actualMessage),
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Reward points message is appeared on shopping cart page.';
    }
}
