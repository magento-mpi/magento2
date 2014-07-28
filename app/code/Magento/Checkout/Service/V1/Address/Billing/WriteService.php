<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\Address\Billing;

class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $quoteAddressFactory;

    /**
     * @param \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
     */
    public function __construct(
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Quote\AddressFactory $quoteAddressFactory
    ) {
        $this->quoteLoader = $quoteLoader;
        $this->quoteAddressFactory = $quoteAddressFactory;
        $this->storeManager = $storeManager;
    }

    /**
     * {@inheritdoc}
     */
    public function setAddress($cartId, $addressData)
    {
        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        /** @var \Magento\Sales\Model\Quote\Address $address */
        $address = $this->quoteAddressFactory->create();

        // validate address id if exists
        if ($addressData->getId()) {
            $address->load($addressData->getId());
            $loadedData = $address->getData();
            if (empty($loadedData)) {
                throw new \Magento\Framework\Exception\NoSuchEntityException(
                    'Invalid address id ' . $addressData->getId()
                );
            }

            if ($address->getCustomerId() != $addressData->getCustomerId()) {
                throw new \Magento\Framework\Exception\InputException(
                    'Address with id ' . $addressData->getId() . ' belongs to another customer'
                );
            }
        }

        $address->setData($addressData->__toArray());
        //set fields with custom logic
        $address->setStreet($addressData->getStreet());
        $address->setRegionId($addressData->getRegion()->getRegionId());
        $address->setRegion($addressData->getRegion()->getRegion());

        $quote->setBillingAddress($address);
        $quote->setDataChanges(true);
        $quote->save();
        return true;
    }
}
