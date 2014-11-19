<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Helper\Session;

use Magento\Customer\Helper\Session\CurrentCustomer;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\AddressInterface;

/**
 * Class CurrentCustomerAddress
 */
class CurrentCustomerAddress
{
    /**
     * @var \Magento\Customer\Helper\Session\CurrentCustomer
     */
    protected $currentCustomer;

    /**
     * @var AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    /**
     * @param CurrentCustomer $currentCustomer
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        CurrentCustomer $currentCustomer,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->currentCustomer = $currentCustomer;
        $this->accountManagement = $accountManagement;
        $this->customerRepository = $customerRepository;
    }

    /**
     * Returns all addresses for current customer
     *
     * @return AddressInterface[]
     */
    public function getCustomerAddresses()
    {
        return $this->customerRepository->getById($this->currentCustomer->getCustomerId())->getAddresses();
    }

    /**
     * Returns default billing address form current customer
     *
     * @return AddressInterface|null
     */
    public function getDefaultBillingAddress()
    {
        return $this->accountManagement->getDefaultBillingAddress($this->currentCustomer->getCustomerId());
    }

    /**
     * Returns default shipping address for current customer
     *
     * @return AddressInterface|null
     */
    public function getDefaultShippingAddress()
    {
        return $this->accountManagement->getDefaultShippingAddress(
            $this->currentCustomer->getCustomerId()
        );
    }
}
