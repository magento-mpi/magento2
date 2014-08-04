<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Checkout\Service\V1\ShippingMethod;


class WriteService implements WriteServiceInterface
{
    /**
     * @var \Magento\Sales\Model\Quote\AddressFactory
     */
    protected $addressFactory;

    /**
     * @var \Magento\Checkout\Service\V1\QuoteLoader
     */
    protected $quoteLoader;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Sales\Model\Quote\AddressFactory $addressFactory,
        \Magento\Checkout\Service\V1\QuoteLoader $quoteLoader
    ) {
        $this->addressFactory = $addressFactory;
        $this->quoteLoader = $quoteLoader;
        $this->storeManager = $storeManager;
    }


    /**
     * {@inheritdoc}
     */
    public function setMethod($cartId, $carrierId, $methodId)
    {
        $quote = $this->quoteLoader->load($cartId, $this->storeManager->getStore()->getId());
        if ($quote->isVirtual()) {
            throw new NoSuchEntityException(
                'Cart contains virtual product(s) only. Shipping method is not applicable.'
            );
        }
        $address = $quote->getShippingAddress();
        //validate shipping info
        $rates = $address->getAllShippingRates();


        $address->setShippingMethod($carrierId . '_' . $methodId);
    }

}
