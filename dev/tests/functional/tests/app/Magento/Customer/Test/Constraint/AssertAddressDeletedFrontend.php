<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\CustomerAccountIndex;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertAddressDeletedFrontend
 * Asserts that deleted customers address does not displays on customer account page
 */
class AssertAddressDeletedFrontend extends AbstractConstraint
{
    const EXPECTED_MESSAGE = 'You have no additional address entries in your address book.';
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Asserts that Asserts that 'Additional Address Entries' contains expected message
     *
     * @param CustomerAccountIndex $customerAccountIndex
     * @return void
     */
    public function processAssert(CustomerAccountIndex $customerAccountIndex)
    {
        $actualText = $customerAccountIndex->getAdditionalAddressBlock()->getBlockText();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(self::EXPECTED_MESSAGE, $actualText),
            'Expected text is absent in Additional Address block'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Expected text is present in Additional Address block';
    }
}
