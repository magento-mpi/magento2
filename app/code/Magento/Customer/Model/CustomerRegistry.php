<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Customer\Model;

use Magento\Customer\Model\CustomerFactory;
use Magento\Exception\NoSuchEntityException;

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

    const REGISTRY_SEPARATOR = ':';

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
            $this->customerRegistryByEmail[$this->getEmailKey($customer)] = $customer;
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
        /** @var Customer $customer */
        $customer = $this->customerFactory->create()->setEmail($customerEmail)->setWebsiteId($websiteId);
        if (isset($this->customerRegistryByEmail[$this->getEmailKey($customer)])) {
            return $this->customerRegistryByEmail[$this->getEmailKey($customer)];
        }

        $customer->loadByEmail($customerEmail);
        if (!$customer->getEmail()) {
            // customer does not exist
            throw new NoSuchEntityException('customerEmail', $customerEmail);
        } else {
            $this->customerRegistryById[$customer->getId()] = $customer;
            $this->customerRegistryByEmail[$this->getEmailKey($customer)] = $customer;
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
        /** @var Customer $customer */
        $customer = $this->customerRegistryById[$customerId];
        unset($this->customerRegistryByEmail[$this->getEmailKey($customer)]);
        unset($this->customerRegistryById[$customerId]);
    }

    /**
     * Remove instance of the Customer Model from registry given an email
     *
     * @param string $customerEmail
     * @return void
     */
    public function removeByEmail($customerEmail)
    {
        /** @var Customer $customer */
        $customer = $this->customerRegistryByEmail[$customerEmail];
        unset($this->customerRegistryByEmail[$this->getEmailKey($customer)]);
        unset($this->customerRegistryById[$customer->getId()]);
    }

    /**
     * Create key for Customer email registry
     *
     * @param Customer $customer
     * @return string
     */
    private function getEmailKey(Customer $customer)
    {
        return $customer->getEmail() . self::REGISTRY_SEPARATOR . $customer->getWebsiteId();
    }
} 