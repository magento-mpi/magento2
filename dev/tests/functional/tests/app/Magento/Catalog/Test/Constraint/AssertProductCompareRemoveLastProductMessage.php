<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Catalog\Test\Page\Product\CatalogProductCompare;

/**
 * Class AssertProductCompareRemoveLastProductMessage
 * Assert message on "Compare Products" page after removing product
 */
class AssertProductCompareRemoveLastProductMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You have no items to compare.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * After removing last product message is appeared on "Compare Products" page
     *
     * @param CatalogProductCompare $comparePage
     * @return void
     */
    public function processAssert(CatalogProductCompare $comparePage)
    {
        $comparePage->open();
        $actualMessage = $comparePage->getCompareProductsBlock()->getEmptyMessage();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Returns a string representation of the object.
     *
     * @return string
     */
    public function toString()
    {
        return 'After removing last product the message appears on "Compare Products" page.';
    }
}
