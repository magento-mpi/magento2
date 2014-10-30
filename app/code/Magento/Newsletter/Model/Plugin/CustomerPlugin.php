<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Plugin;

use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Newsletter\Model\SubscriberFactory;

class CustomerPlugin
{
    /**
     * Factory used for manipulating newsletter subscriptions
     *
     * @var SubscriberFactory
     */
    private $subscriberFactory;

    /**
     * Constructor
     *
     * @param SubscriberFactory $subscriberFactory
     */
    public function __construct(
        SubscriberFactory $subscriberFactory
    ) {
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Plugin after create customer that updates any newsletter subscription that may have existed.
     *
     * @param CustomerAccountServiceInterface $subject
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @return CustomerInterface
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterCreateCustomer(CustomerAccountServiceInterface $subject, CustomerInterface $customer)
    {
        $this->subscriberFactory->create()->updateSubscription($customer->getId());

        return $customer;
    }

    /**
     * Plugin around updating a customer account that updates any newsletter subscription that may have existed.
     *
     * @param CustomerAccountServiceInterface $subject
     * @param callable $updateCustomer
     * @param string $customerId
     * @param CustomerDetails $customerDetails
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundUpdateCustomer(
        CustomerAccountServiceInterface $subject,
        callable $updateCustomer,
        $customerId,
        CustomerDetails $customerDetails
    ) {
        $result = $updateCustomer($customerId, $customerDetails);

        $this->subscriberFactory->create()->updateSubscription($customerDetails->getCustomer()->getId());

        return $result;
    }

    /**
     * Plugin after delete customer that updates any newsletter subscription that may have existed.
     *
     * @param CustomerAccountServiceInterface $subject
     * @param callable $deleteCustomer Function we are wrapping around
     * @param int $customerId Input to the function
     * @return bool
     */
    public function aroundDeleteCustomer(
        CustomerAccountServiceInterface $subject,
        callable $deleteCustomer,
        $customerId
    ) {
        $customer = $subject->getCustomer($customerId);

        $result = $deleteCustomer($customerId);

        /** @var \Magento\Newsletter\Model\Subscriber $subscriber */
        $subscriber = $this->subscriberFactory->create();
        $subscriber->loadByEmail($customer->getEmail());
        if ($subscriber->getId()) {
            $subscriber->delete();
        }

        return $result;
    }
}
