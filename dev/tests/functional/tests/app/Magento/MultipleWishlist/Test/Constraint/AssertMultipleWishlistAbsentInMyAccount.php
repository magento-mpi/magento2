<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\MultipleWishlist\Test\Constraint;

/**
 * Class AssertMultipleWishlistAbsentInMyAccount
 * Assert Multiple wish list doesn't exist on "My Account" page
 */
class AssertMultipleWishlistAbsentInMyAccount extends AbstractAssertMultipleWishlistExist
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'high';

    /**
     * Assert wish list is absent
     *
     * @param bool $isPresent
     * @return void
     */
    protected function assert($isPresent)
    {
        \PHPUnit_Framework_Assert::assertFalse($isPresent, 'Multiple wish list exist on "My Account" page.');
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Multiple wish list is absent on "My Account" page.';
    }
}
