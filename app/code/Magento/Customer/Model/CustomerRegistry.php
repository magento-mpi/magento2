<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Registry for \Magento\Customer\Model\Customer
 */
class CustomerRegistry
{
    /**
     * @var CustomerFactory
     */
    private $customerFactory;

    /**
     * @var array
     */
    private $customerRegistryById = [];

    /**
     * @var array
     */
    private $customerRegistryByEmail = [];

    /**
     * Constructor
     *
     * @param CustomerFactory $customerFactory
     */
    public function __construct(CustomerFactory $customerFactory)
    {
        $this->customerFactory = $customerFactory;
    }

    /**
     * Retrieve Customer Model from registry given an id
     *
     * @param string $customerId
     * @return Customer
     * @throws NoSuchEntityException
     */
    public function retrieve($customerId)
    {
        if (isset($this->customerRegistryById[$customerId])) {
            return $this->customerRegistryById[$customerId];
        }
        /** @var Customer $customer */
        $customer = $this->customerFactory->create()->load($customerId);
        if (!$customer->getId()) {
            // customer does not exist
            throw new NoSuchEntityException('customerId', $customerId);
        } else {
            $this->customerRegistryById[$customerId] = $customer;
            $this->customerRegistryByEmail[$customer->getEmail() . $customer->getWebsiteId()] = $customer;
            return $customer;
        }
    }

    /**
     * Retrieve Customer Model from registry given an email
     *
     * @param string $customerEmail
     * @param string $websiteId
     * @return Customer
     * @throws NoSuchEntityException
     */
    public function retrieveByEmail($customerEmail, $websiteId)
    {
        $emailKey = $customerEmail . $websiteId;
        if (isset($this->customerRegistryByEmail[$emailKey])) {
            return $this->customerRegistryByEmail[$emailKey];
        }

        /** @var Customer $customer */
        $customer = $this->customerFactory->create();

        if (isset($websiteId)) {
            $customer->setWebsiteId($websiteId);
        }

        $customer->loadByEmail($customerEmail);
        if (!$customer->getEmail()) {
            // customer does not exist
            throw (new NoSuchEntityException('email', $customerEmail))->addField('websiteId', $websiteId);
        } else {
            $this->customerRegistryById[$customer->getId()] = $customer;
            $this->customerRegistryByEmail[$emailKey] = $customer;
            return $customer;
        }
    }

    /**
     * Remove instance of the Customer Model from registry given an id
     *
     * @param int $customerId
     * @return void
     */
    public function remove($customerId)
    {
        if (isset($this->customerRegistryById[$customerId])) {
            /** @var Customer $customer */
            $customer = $this->customerRegistryById[$customerId];
            unset($this->customerRegistryByEmail[$customer->getEmail() . $customer->getWebsiteId()]);
            unset($this->customerRegistryById[$customerId]);
        }
    }

    /**
     * Remove instance of the Customer Model from registry given an email
     *
     * @param string $customerEmail
     * @param string $websiteId
     * @return void
     */
    public function removeByEmail($customerEmail, $websiteId)
    {
        $emailKey = $customerEmail . $websiteId;
        if ($emailKey) {
            /** @var Customer $customer */
            $customer = $this->customerRegistryByEmail[$emailKey];
            unset($this->customerRegistryByEmail[$emailKey]);
            unset($this->customerRegistryById[$customer->getId()]);
        }
    }
}
