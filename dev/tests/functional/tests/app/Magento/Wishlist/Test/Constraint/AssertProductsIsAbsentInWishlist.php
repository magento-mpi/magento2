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
 * Class AssertProductsIsAbsentInWishlist
 * Assert products is absent in Wishlist on Frontend
 */
class AssertProductsIsAbsentInWishlist extends AbstractConstraint
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
     * @param InjectableFixture[] $products
     * @param CustomerInjectable $customer
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountLogin $customerAccountLogin
     * @param CustomerAccountLogout $customerAccountLogout
     * @return void
     */
    public function processAssert(
        CustomerAccountIndex $customerAccountIndex,
        WishlistIndex $wishlistIndex,
        $products,
        CustomerInjectable $customer,
        CmsIndex $cmsIndex,
        CustomerAccountLogin $customerAccountLogin,
        CustomerAccountLogout $customerAccountLogout
    ) {
        $customerAccountLogout->open();
        $cmsIndex->getLinksBlock()->openLink('Log In');
        $customerAccountLogin->getLoginBlock()->login($customer);
        $customerAccountIndex->open()->getAccountMenuBlock()->openMenuItem("My Wish List");
        $itemBlock = $wishlistIndex->getWishlistBlock()->getProductItemsBlock();

        foreach ($products as $itemProduct) {
            $productName = $itemProduct->getName();
            \PHPUnit_Framework_Assert::assertFalse(
                $itemBlock->getItemProductByName($productName)->isVisible(),
                'Product \'' . $productName . '\' is present in Wishlist on Frontend.'
            );
        }
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
