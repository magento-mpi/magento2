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
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;

/**
 * Class AssertMassActionSuccessUpdateMessage
 * Assert update message is appears on customer grid (Customers > All Customers)
 */
class AssertMassActionSuccessUpdateMessage extends AbstractConstraint
{
    /**
     * Text value to be checked
     */
    const UPDATE_MESSAGE = 'A total of %d record(s) were updated.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert update message is appears on customer grid (Customers > All Customers)
     *
     * @param CustomerInjectable|CustomerInjectable[] $customer
     * @param CustomerIndex $pageCustomerIndex
     * @return void
     */
    public function processAssert($customer, CustomerIndex $pageCustomerIndex)
    {
        $customers = is_array($customer) ? $customer : [$customer];
        $customerCount = count($customers);
        $actualMessage = $pageCustomerIndex->getMessagesBlock()->getSuccessMessages();
        \PHPUnit_Framework_Assert::assertEquals(sprintf(self::UPDATE_MESSAGE, $customerCount), $actualMessage);
    }

    /**
     * Returns a string representation of the object
     *
     * @return string
     */
    public function toString()
    {
        return 'Assert that update message is displayed.';
    }
}
