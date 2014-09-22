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
use Magento\Checkout\Test\Page\CheckoutCart;
use Mtf\Client\Driver\Selenium\Browser;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardPointsMessageOnShoppingCart
 * Assert that reward points message is displayed on shopping cart page
 */
class AssertRewardPointsMessageOnShoppingCart extends AbstractConstraint
{
    /**
     * Message about reward points on checkout page
     */
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
     * @param Browser $browser
     * @param CatalogProductSimple $product
     * @param CatalogProductView $productView
     * @param CheckoutCart $checkoutCart
     * @param string $checkoutReward
     * @return void
     */
    public function processAssert(
        Browser $browser,
        CatalogProductSimple $product,
        CatalogProductView $productView,
        CheckoutCart $checkoutCart,
        $checkoutReward
    ) {
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $productView->getViewBlock()->clickAddToCartButton();

        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::CHECKOUT_REWARD_MESSAGE, $checkoutReward),
            trim($checkoutCart->getCheckoutTooltipBlock()->getRewardMessages()),
            'Wrong message about checkout reward points is displayed.'
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
