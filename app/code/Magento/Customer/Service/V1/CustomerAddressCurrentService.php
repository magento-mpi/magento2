<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Customer\Service\V1;

/**
 * Class CustomerAddressCurrentService
 */
class CustomerAddressCurrentService implements \Magento\Customer\Service\V1\CustomerAddressCurrentServiceInterface
{
    /**
     * @var CustomerCurrentService
     */
    protected $customerCurrentService;

    /**
     * @var CustomerAddressService
     */
    protected $customerAddressService;

    public function __construct(
        \Magento\Customer\Service\V1\CustomerCurrentService $customerCurrentService,
        \Magento\Customer\Service\V1\CustomerAddressService $customerAddressService
    ) {
        $this->customerCurrentService = $customerCurrentService;
        $this->customerAddressService = $customerAddressService;
    }

    /**
     * Returns all addresses for current customer
     *
     * @return array|Dto\Address[]
     */
    public function getCustomerAddresses()
    {
        return $this->customerAddressService
            ->getAddresses($this->customerCurrentService->getCustomerId());
    }

    /**
     * Returns default billing address form current customer
     *
     * @return Dto\Address|null
     */
    public function getDefaultBillingAddress()
    {
        return $this->customerAddressService
            ->getDefaultBillingAddress($this->customerCurrentService->getCustomerId());
    }

    /**
     * Returns default shipping address for current customer
     *
     * @return Dto\Address|null
     */
    public function getDefaultShippingAddress()
    {
        return $this->customerAddressService
            ->getDefaultShippingAddress($this->customerCurrentService->getCustomerId());
    }
}
