<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductView;
use Mtf\Fixture\FixtureInterface;

/**
 * Class AssertProductCompareSuccessAddMessage
 */
class AssertProductCompareSuccessAddMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You added product %s to the comparison list.';

    /* tags */
     const SEVERITY = 'high';
     /* end tags */

    /**
     * Assert success message is presented on page
     *
     * @param CatalogProductView $catalogProductView
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductView $catalogProductView, FixtureInterface $product)
    {
        $successMessage = sprintf(self::SUCCESS_MESSAGE, $product->getName());
        $actualMessage = $catalogProductView->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            $successMessage,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . $successMessage
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product has been added compare products list.';
    }
}
