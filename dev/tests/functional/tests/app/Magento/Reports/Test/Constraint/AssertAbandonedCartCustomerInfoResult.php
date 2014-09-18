<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Reports\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Reports\Test\Page\Adminhtml\AbandonedCarts;

/**
 * Class AssertAbandonedCartCustomerInfoResult
 * Assert customer info in Abandoned Carts report
 */
class AssertAbandonedCartCustomerInfoResult extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert customer info in Abandoned Carts report (Reports > Abandoned carts):
     * – name and email
     * – products and qty
     * – created and updated date
     *
     * @param AbandonedCarts $abandonedCarts
     * @param array $products
     * @param CustomerInjectable $customer
     * @return void
     */
    public function processAssert(AbandonedCarts $abandonedCarts, $products, CustomerInjectable $customer)
    {
        $abandonedCarts->open();
        $qty = 0;
        foreach ($products as $product) {
            $qty += $product->getCheckoutData()['options']['qty'];
        }
        $filter = [
            'customer_name' => $customer->getFirstname() . " " . $customer->getLastname(),
            'email' => $customer->getEmail(),
            'items_count' => count($products),
            'items_qty' => $qty,
            'created_at' => date('m/j/Y'),
            'updated_at' => date('m/j/Y')
        ];
        $abandonedCarts->getGridBlock()->search($filter);
        $filter['created_at'] = date('M j, Y');
        $filter['updated_at'] = date('M j, Y');
        \PHPUnit_Framework_Assert::assertTrue(
            $abandonedCarts->getGridBlock()->isRowVisible($filter, false, false),
            'Customer info is absent in Abandoned Carts report grid.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer info in Abandoned Carts report. grid';
    }
}
