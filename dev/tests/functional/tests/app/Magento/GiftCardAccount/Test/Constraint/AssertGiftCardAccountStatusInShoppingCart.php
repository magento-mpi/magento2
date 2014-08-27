<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GiftCardAccount\Test\Constraint;

use Mtf\Client\Browser;
use Magento\Checkout\Test\Page\CheckoutCart;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\GiftCardAccount\Test\Fixture\GiftCardAccount;
use Magento\Catalog\Test\Page\Product\CatalogProductView;

/**
 * Class AssertGiftCardAccountStatusInShoppingCart
 * Assert that created gift card account can be verified on the frontend in Shopping Cart
 */
class AssertGiftCardAccountStatusInShoppingCart extends AbstractAssertGiftCardAccountOnFrontend
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that created gift card account can be verified on the frontend in Shopping Cart
     *
     * @param CheckoutCart $checkoutCart
     * @param GiftCardAccount $giftCardAccount
     * @param CustomerInjectable $customer
     * @param CatalogProductSimple $product
     * @param CatalogProductView $catalogProductView
     * @param Browser $browser
     * @param string $code
     * @return void
     */
    public function processAssert(
        CheckoutCart $checkoutCart,
        GiftCardAccount $giftCardAccount,
        CustomerInjectable $customer,
        CatalogProductSimple $product,
        CatalogProductView $catalogProductView,
        Browser $browser,
        $code
    ) {
        $this->login($customer);
        $browser->open($_ENV['app_frontend_url'] . $product->getUrlKey() . '.html');
        $catalogProductView->getViewBlock()->clickAddToCart();
        $data = $giftCardAccount->getData();
        $data['code'] = $code;
        $checkoutCart->getGiftCardAccountBlock()->checkStatusAndBalance($code);
        $result = $this->prepareData($data, $checkoutCart);
        \PHPUnit_Framework_Assert::assertEquals(
            $result['fixtureData'],
            $result['pageData'],
            'Wrong success message is displayed.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Gift card account data is correct on the frontend in Shopping Cart.';
    }
}
