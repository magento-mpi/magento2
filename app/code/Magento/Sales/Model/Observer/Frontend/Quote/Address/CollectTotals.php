<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Observer\Frontend\Quote\Address;

class CollectTotals
{
    /**
     * Customer address
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $customerAddress;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $customerData;

    /**
     * @var VatValidator
     */
    protected $vatValidator;

    /**
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Customer\Helper\Data $customerData
     * @param VatValidator $vatValidator
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Customer\Helper\Data $customerData,
        VatValidator $vatValidator
    ) {
        $this->customerData = $customerData;
        $this->customerAddress = $customerAddress;
        $this->vatValidator = $vatValidator;
    }

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param \Magento\Event\Observer $observer
     */
    public function dispatch(\Magento\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Quote\Address $quoteAddress */
        $quoteAddress = $observer->getQuoteAddress();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $quoteAddress->getQuote();

        /** @var \Magento\Customer\Model\Customer $customer */
        $customer = $quote->getCustomer();

        /** @var \Magento\Core\Model\Store $store */
        $store = $customer->getStore();

        if ($customer->getDisableAutoGroupChange() || false == $this->vatValidator->isEnabled($quoteAddress, $store)) {
            return;
        }

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber   = $quoteAddress->getVatId();
        $groupId = null;

        if (empty($customerVatNumber) || false == $this->customerData->isCountryInEU($customerCountryCode)) {
            $groupId = $customer->getId()
                ? $this->customerData->getDefaultCustomerGroupId($store)
                : \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;
        } else {
            // Magento always has to emulate group even if customer uses default billing/shipping address
            $groupId = $this->customerData->getCustomerGroupIdBasedOnVatNumber(
                $customerCountryCode, $this->vatValidator->validate($quoteAddress, $store), $store
            );
        }

        if ($groupId) {
            $quoteAddress->setPrevQuoteCustomerGroupId($quote->getCustomerGroupId());
            $customer->setGroupId($groupId);
            $quote->setCustomerGroupId($groupId);
        }
    }
}
