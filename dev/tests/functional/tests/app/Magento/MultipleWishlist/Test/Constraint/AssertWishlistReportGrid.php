<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\MultipleWishlist\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\MultipleWishlist\Test\Fixture\MultipleWishlist;
use Magento\MultipleWishlist\Test\Page\Adminhtml\CustomerWishlistReport;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertWishlistReportGrid
 * Assert that added to the customer wish list products present in the grid and products have correct values
 */
class AssertWishlistReportGrid extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Assert that added to the customer wish list products present in the grid and products have correct values
     *
     * @param CustomerWishlistReport $customerWishlistReport
     * @param MultipleWishlist $multipleWishlist
     * @param CustomerInjectable $customer
     * @param array $products
     * @param array $wishlist
     * @return void
     */
    public function processAssert(
        CustomerWishlistReport $customerWishlistReport,
        MultipleWishlist $multipleWishlist,
        CustomerInjectable $customer,
        array $products,
        array $wishlist
    ) {
        $customerWishlistReport->open();
        foreach ($products as $key => $product) {
            $filter = [
                'wishlist_name' => $multipleWishlist->getName(),
                'visibility' => $multipleWishlist->getVisibility() === 'No' ? 'Private' : 'Public',
                'item_comment' => $wishlist[$key]['description'],
                'customer_name' => $customer->getFirstname() . ' ' . $customer->getLastname(),
                'product_name' => $product->getName(),
                'product_sku' => $product->getSku(),
            ];
            $errorMessage = implode(', ', $filter);
            \PHPUnit_Framework_Assert::assertTrue(
                $customerWishlistReport->getWishlistReportGrid()->isRowVisible($filter),
                'Wish list with following data: \'' . $errorMessage . '\' '
                . 'is absent in Customer Wish List Report grid.'
            );
        }
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Wish list is present in Customer Wish List Report grid.';
    }
}
