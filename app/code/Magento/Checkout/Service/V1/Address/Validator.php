<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address;

class Validator
{
    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $customerFactory;

    /**
     * @param \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     */
    public function __construct(
        \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory
    ) {
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->customerFactory = $customerFactory;
    }

    /**
     * Validate data object fields
     *
     * @param \Magento\Checkout\Service\V1\Data\Cart\Address $addressData
     * @return bool
     * @throws \Magento\Framework\Exception\InputException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function validate($addressData)
    {
        //validate customer id
        if ($addressData->getCustomerId()) {
            $customer = $this->customerFactory->create();
            $customer->load($addressData->getCustomerId());
            if (!$customer->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    'Invalid customer id ' . $addressData->getCustomerId()
                );
            }
        }

        // validate address id
        if ($addressData->getId()) {
            $address = $this->quoteAddressFactory->create();
            $address->load($addressData->getId());
            if (!$address->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    'Invalid address id ' . $addressData->getId()
                );
            }

            // check correspondence between customer id and address id
            if ($addressData->getCustomerId()) {
                if ($address->getCustomerId() != $addressData->getCustomerId()) {
                    throw new \Magento\Framework\Exception\InputException(
                        'Address with id ' . $addressData->getId() . ' belongs to another customer'
                    );
                }
            }
        }
        return true;
    }
}
