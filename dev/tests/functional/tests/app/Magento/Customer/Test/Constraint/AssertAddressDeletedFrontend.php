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
 * Assert that deleted customers address is absent in Address Book in Customer Account
 */
class AssertAddressDeletedFrontend extends AbstractConstraint
{
    /**
     * Expected message
     */
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
        $customerAccountIndex->open();
        $customerAccountIndex->getAccountMenuBlock()->openMenuItem('Address Book');
        $actualText = $customerAccountIndex->getAdditionalAddressBlock()->getBlockText();
        \PHPUnit_Framework_Assert::assertTrue(
            in_array(self::EXPECTED_MESSAGE, $actualText),
            'Expected text is absent in Additional Address block.'
        );
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Deleted address is absent in "Additional Address Entries" block.';
    }
}
