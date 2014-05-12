<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerGroupNew;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerGroupAlreadyExists
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerGroupAlreadyExists extends AbstractConstraint
{
    const SUCCESS_MESSAGE = 'Customer Group already exists.';

    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert that customer group already exist
     *
     * @param CustomerGroupNew $customerGroupNew
     * @return void
     */
    public function processAssert(CustomerGroupNew $customerGroupNew)
    {
        $actualMessage = $customerGroupNew->getMessageBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            self::SUCCESS_MESSAGE,
            $actualMessage,
            'Wrong error message is displayed.'
        );
    }

    /**
     * Success assert of customer group already exist
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group already exist.';
    }
}
