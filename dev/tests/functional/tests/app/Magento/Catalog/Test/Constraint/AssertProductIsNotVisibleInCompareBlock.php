<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Catalog\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountIndex;

/**
 * Class AssertProductIsNotVisibleInCompareBlock
 * Assert the product is not displayed on Compare Products block on my account page
 */
class AssertProductIsNotVisibleInCompareBlock extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You have no items to compare.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert the product is not displayed on Compare Products block on my account page
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(CmsIndex $cmsIndex, CustomerAccountIndex $customerAccountIndex)
    {
        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink("My Account");
        $actualMessage = $customerAccountIndex->getCompareProductsBlock()->getEmptyMessage();

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
        return 'The message appeared on Compare Products block on my account page.';
    }
}
