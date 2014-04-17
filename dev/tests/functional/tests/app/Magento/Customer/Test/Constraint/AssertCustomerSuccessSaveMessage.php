<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Mtf\Constraint\AbstractConstraint;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Class AssertCustomerSuccessSaveMessage
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerSuccessSaveMessage extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'You saved the customer.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that success message is displayed after customer save
     *
     * @param CustomerIndex $pageCustomerIndex
     * @return void
     */
    public function processAssert(CustomerIndex $pageCustomerIndex)
    {
        sleep(1);

        $actualMessage = $pageCustomerIndex->getBlockMessages()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong success message is displayed.'
            . "\nExpected: " . self::SUCCESS_MESSAGE
            . "\nActual: " . $actualMessage
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return 'Assert that message "You saved the customer." is displayed';
    }
}
