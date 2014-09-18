<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;
use Magento\Wishlist\Test\Page\WishlistIndex;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountLogin;
use Magento\Customer\Test\Page\CustomerAccountLogout;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertProductIsAbsentInWishlist
 * Assert that product is absent in Wishlist on Frontend
 */
class AssertProductIsAbsentInWishlist extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that product is not present in Wishlist on Frontend
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @param WishlistIndex $wishlistIndex
     * @param InjectableFixture $product
     * @param CustomerInjectable $customer
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function processAssert(
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        InjectableFixture $product,
        CustomerInjectable $customer,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $productName = $product->getName();
        $customerAccountLogout->open();
        $cmsIndex->getLinksBlock()->openLink('Log In');
        $customerAccountLogin->getLoginBlock()->login($customer);
        $customerAccountIndex->open()->getAccountMenuBlock()->openMenuItem("My Wish List");
        \PHPUnit_Framework_Assert::assertFalse(
            $wishlistIndex->getWishlistBlock()->getProductItemsBlock()->isProductPresent($productName),
            'Product \'' . $productName . '\' is present in Wishlist on Frontend.'
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'Product is absent in Wishlist on Frontend.';
    }
}
