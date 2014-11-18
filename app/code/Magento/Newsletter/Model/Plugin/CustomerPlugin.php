<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Newsletter\Model\Plugin;

use Magento\Customer\Service\V1\Data\CustomerDetails;
use Magento\Newsletter\Model\SubscriberFactory;

/**
 * Class CustomerPlugin
 */
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
    public function __construct(SubscriberFactory $subscriberFactory)
    {
        $this->subscriberFactory = $subscriberFactory;
    }

    /**
     * Plugin around updating a customer account that updates any newsletter subscription that may have existed.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param callable $updateCustomer
     * @param \Magento\Customer\Api\Data\CustomerInterface $customer
     * @param string|null $passwordHash
     * @return bool
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function aroundSave(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        callable $updateCustomer,
        \Magento\Customer\Api\Data\CustomerInterface $customer,
        $passwordHash = null
    ) {
        $result = $updateCustomer($customer, $passwordHash);
        $this->subscriberFactory->create()->updateSubscription($result->getId());

        return $result;
    }

    /**
     * Plugin after delete customer that updates any newsletter subscription that may have existed.
     *
     * @param \Magento\Customer\Api\CustomerRepositoryInterface $subject
     * @param callable $deleteCustomer Function we are wrapping around
     * @param int $customerId Input to the function
     * @return bool
     */
    public function aroundDeleteById(
        \Magento\Customer\Api\CustomerRepositoryInterface $subject,
        callable $deleteCustomer,
        $customerId
    ) {
        $customer = $subject->getById($customerId);

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
