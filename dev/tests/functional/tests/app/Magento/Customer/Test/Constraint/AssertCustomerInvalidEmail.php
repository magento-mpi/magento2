<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndexNew;

/**
 * Class AssertCustomerInvalidEmail
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerInvalidEmail extends AbstractConstraint
{
    const CORRECT_EMAIL_MESSAGE_TPL = 'Please correct this email address: "%email%".';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'middle';

    /**
     * Assert that error message "Please correct this email address: "%email%"." is displayed after customer with invalid email save
     *
     * @param CustomerInjectable $customer
     * @param CustomerIndexNew $pageCustomerIndexNew
     * @return void
     */
    public function processAssert(CustomerInjectable $customer, CustomerIndexNew $pageCustomerIndexNew)
    {
        $expectMessage = str_replace('%email%', $customer->getEmail(), self::CORRECT_EMAIL_MESSAGE_TPL);
        $actualMessage = $pageCustomerIndexNew->getBlockMessages()->getErrorMessages();

        \PHPUnit_Framework_Assert::assertEquals(
            $expectMessage,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . $expectMessage
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Assert that error message "Please correct this email address: "%email%"." is displayed';
    }
}
