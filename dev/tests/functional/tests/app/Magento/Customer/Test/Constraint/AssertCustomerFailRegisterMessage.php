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
 * Class AssertCustomerFailRegisterMessage
 */
class AssertCustomerFailRegisterMessage extends AbstractConstraint
{
    const ERROR_MESSAGE_ACCOUNT = 'There is already an account with this email address. If you are sure that ';
    const ERROR_MESSAGE_EMAIL = 'it is your email address, click here to get your password and access your account.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that error message is displayed on "Create New Customer Account" page(frontend)
     *
     * @param CustomerAccountCreate $registerPage
     * @return void
     */
    public function processAssert(CustomerAccountCreate $registerPage)
    {
        $actualMessage = $registerPage->getMessagesBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::ERROR_MESSAGE_ACCOUNT . self::ERROR_MESSAGE_EMAIL,
            $actualMessage,
            'Wrong error message is displayed.'
            . "\nExpected: " . self::ERROR_MESSAGE_ACCOUNT . self::ERROR_MESSAGE_EMAIL
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * Text error message is displayed
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that error message is displayed';
    }
}
