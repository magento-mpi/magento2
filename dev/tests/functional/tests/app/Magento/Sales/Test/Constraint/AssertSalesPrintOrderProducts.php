<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Sales\Test\Constraint;

use Magento\Sales\Test\Page\SalesGuestPrint;
use Mtf\Constraint\AbstractConstraint;
use Mtf\Fixture\InjectableFixture;

/**
 * Assert that products printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderProducts extends AbstractConstraint
{
    /**
     * Template for error message.
     */
    const ERROR_MESSAGE = "Product with name: '%s' was not found on sales guest print page.\n";

    /**
     * Constraint severeness.
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that products printed correctly on sales guest print page.
     *
     * @param SalesGuestPrint $salesGuestPrint
     * @param InjectableFixture[] $products
     * @return void
     */
    public function processAssert(SalesGuestPrint $salesGuestPrint, array $products)
    {
        $errors = '';
        foreach ($products as $product) {
            if (!$salesGuestPrint->getViewBlock()->getItemBlock()->isItemVisible($product)) {
                $errors .= sprintf(self::ERROR_MESSAGE, $product->getName());
            }
        }

        \PHPUnit_Framework_Assert::assertEmpty($errors, $errors);
    }

    /**
     * Returns a string representation of successful assertion.
     *
     * @return string
     */
    public function toString()
    {
        return "Products were printed correctly.";
    }
}
