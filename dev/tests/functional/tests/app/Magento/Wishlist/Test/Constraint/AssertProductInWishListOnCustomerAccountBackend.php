<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Wishlist\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexEdit;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Class AssertProductInWishListOnCustomerAccountBackend
 * Assert that product added to wishlist is present on Customers account on backend
 * - in section Customer Activities - Wishlist
 */
class AssertProductInWishListOnCustomerAccountBackend extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that products added to wishlist are present on Customers account on backend.
     *
     * @param CustomerIndex $customerIndex
     * @param CustomerInjectable $customer
     * @param CustomerIndexEdit $customerIndexEdit
     * @param InjectableFixture $product
     * @return void
     */
    public function processAssert(
        CustomerIndex $customerIndex,
        CustomerInjectable $customer,
        CustomerIndexEdit $customerIndexEdit,
        InjectableFixture $product
    ) {
        $customerIndex->open();
        $customerIndex->getCustomerGridBlock()->searchAndOpen(['email' => $customer->getEmail()]);
        $customerIndexEdit->getCustomerForm()->openTab('wishlist');
        /** @var \Magento\Wishlist\Test\Block\Adminhtml\Customer\Edit\Tab\Wishlist\Grid $wishlistGrid */
        $wishlistGrid = $customerIndexEdit->getCustomerForm()->getTabElement('wishlist')->getSearchGridBlock();

        \PHPUnit_Framework_Assert::assertTrue(
            $wishlistGrid->isRowVisible(['product_name' => $product->getName()]),
            $product->getName() . " is not visible in customer wishlist on backend."
        );

    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return "Product is visible in customer wishlist on backend.";
    }
}
