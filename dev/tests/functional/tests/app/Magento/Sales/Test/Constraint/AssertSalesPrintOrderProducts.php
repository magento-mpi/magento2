<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Test\Constraint;

use Mtf\Fixture\InjectableFixture;
use Mtf\Constraint\AbstractConstraint;
use Magento\Sales\Test\Page\SalesGuestPrint;

/**
 * Assert that products printed correctly on sales guest print page.
 */
class AssertSalesPrintOrderProducts extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    /**
     * Template for error message.
     */
    const ERROR_MESSAGE = "Product with name: '%s' was not found on sales guest print page.\n";

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
