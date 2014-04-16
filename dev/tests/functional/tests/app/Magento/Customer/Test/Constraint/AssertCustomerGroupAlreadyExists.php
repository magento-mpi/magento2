<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Page\Adminhtml\CustomerGroupIndex;
use Magento\Customer\Test\Page\Adminhtml\CustomerGroupNew;
use Magento\Customer\Test\Fixture\CustomerGroup;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerGroupAlreadyExists
 *
 * @package Constraint
 */
class AssertCustomerGroupAlreadyExists extends AbstractConstraint
{
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
     * @param CustomerGroupIndex $customerGroupIndex
     * @param CustomerGroup $customerGroup
     * @return void
     */
    public function processAssert(
        CustomerGroupNew $customerGroupNew,
        CustomerGroupIndex $customerGroupIndex,
        CustomerGroup $customerGroup
    ) {
        $actualMessage = $customerGroupNew->getMessageBlock()->getErrorMessages();
        \PHPUnit_Framework_Assert::assertEquals(
            'Customer Group already exists. { code: INVALID_FIELD_VALUE code: ' . $customerGroup->getCode(
            ) . 'params: [] }',
            $actualMessage,
            'Wrong error message is displayed.'
        );

        $customerGroupIndex->open();
    }

    /**
     * Text of customer group already exist
     *
     * @return string
     */
    public function toString()
    {
        return 'Customer group already exist.';
    }
}
