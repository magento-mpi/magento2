<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Fixture\FixtureInterface;
use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Class AssertProductCompareSuccessRemoveMessage
 * Assert message is appeared on "Compare Products" block on myAccount page
 */
class AssertProductCompareSuccessRemoveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You removed product %s from the comparison list.';

    /* tags */
     const SEVERITY = 'low';
     /* end tags */

    /**
     * Assert message is appeared on "Compare Products" block on myAccount page
     *
     * @param CatalogProductCompare $catalogProductCompare
     * @param FixtureInterface $product
     * @return void
     */
    public function processAssert(CatalogProductCompare $catalogProductCompare, FixtureInterface $product)
    {
        $successMessage = sprintf(self::SUCCESS_MESSAGE, $product->getName());
        $actualMessage = $catalogProductCompare->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals($successMessage, $actualMessage, 'Wrong success message is displayed.');
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Product has been removed from compare products list.';
    }
}
