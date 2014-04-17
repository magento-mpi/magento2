<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Test\Constraint;

use Magento\Customer\Test\Fixture\CustomerInjectable;
use Magento\Customer\Test\Page\Adminhtml\CustomerIndex;
use Magento\Customer\Test\Page\Adminhtml\NewsletterSubscriber;
use Mtf\Constraint\AbstractConstraint;

/**
 * Class AssertCustomerIsSubscribedToNewsletter
 *
 * @package Magento\Customer\Test\Constraint
 */
class AssertCustomerIsSubscribedToNewsletter extends AbstractConstraint
{
    /**
     * Constraint severeness
     *
     * @var string
     */
    protected $severeness = 'low';

    /**
     * Assert customer is subscribed to newsletter
     *
     * @param CustomerInjectable $customer
     * @param NewsletterSubscriber $subscriber
     * @return void
     */
    public function processAssert (
        CustomerInjectable $customer,
        NewsletterSubscriber $subscriber
    ) {
        $filter = [
            'email' => $customer->getEmail(),
            'firstname' => $customer->getFirstname(),
            'lastname' => $customer->getLastname(),
            'status' => 'Subscribed'
        ];

        $subscriber->open();
        \PHPUnit_Framework_Assert::assertTrue(
            $subscriber->getSubscriberGrid()->isRowVisible($filter),
            'Customer with email \'' . $customer->getEmail() . '\' is absent in Newsletter Subscribers grid.'
        );
    }

    /**
     * @return string
     */
    public function toString()
    {
        return "Customer is subscribed to newsletter";
    }
}
