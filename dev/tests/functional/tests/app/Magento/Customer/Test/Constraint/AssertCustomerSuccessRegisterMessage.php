<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\CustomerAccountCreate;

/**
 * Class AssertCustomerSuccessRegisterMessage
 *
 */
class AssertCustomerSuccessRegisterMessage extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'low';
    /* end tags */

    const SUCCESS_MESSAGE = 'Thank you for registering with Main Website Store.';

    /**
     * Assert that success message is displayed after customer registered on frontend
     *
     * @param CustomerAccountCreate $registerPage
     * @return void
     */
    public function processAssert(CustomerAccountCreate $registerPage)
    {
        $actualMessage = $registerPage->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text of success register message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return "Customer is successfully registered.";
    }
}
