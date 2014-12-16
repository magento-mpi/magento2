<?php
/**
 * @copyright Copyright (c) 2014 X.commerce, Inc. (http://www.magentocommerce.com)
 */

namespace Magento\Reward\Test\Constraint;

use Magento\Cms\Test\Page\CmsIndex;
use Magento\Customer\Test\Page\CustomerAccountCreate;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertRewardPointsMessageOnCustomerRegistration
 * Assert that reward points message is appeared on the Create New Customer Account page
 */
class AssertRewardPointsMessageOnCustomerRegistration extends AbstractConstraint
{
    /* tags */
    const SEVERITY = 'high';
    /* end tags */

    /**
     * Message about reward points on registration page
     */
    const REGISTRATION_REWARD_MESSAGE = 'Register on our site now and earn %d Reward points.';

    /**
     * Assert that reward points message is appeared on the Create New Customer Account page
     *
     * @param CmsIndex $cmsIndex
     * @param CustomerAccountCreate $customerAccountCreate
     * @param string $registrationReward
     * @return void
     */
    public function processAssert(
        CmsIndex $cmsIndex,
        CustomerAccountCreate $customerAccountCreate,
        $registrationReward
    ) {
        $cmsIndex->open();
        $cmsIndex->getLinksBlock()->openLink('Register');

        \PHPUnit_Framework_Assert::assertEquals(
            sprintf(self::REGISTRATION_REWARD_MESSAGE, $registrationReward),
            trim($customerAccountCreate->getTooltipBlock()->getRewardMessages()),
            'Wrong message about registration reward is displayed.'
        );
    }

    /**
     * Returns a string representation of successful assertion
     *
     * @return string
     */
    public function toString()
    {
        return 'Reward points message is appeared on create new customer account page.';
    }
}
