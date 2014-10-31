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
     * @var \Magento\Customer\Helper\Address
     */
    protected $customerAddressHelper;

    /**
     * @var \Magento\Customer\Helper\Data
     */
    protected $customerHelper;

    /**
     * @var VatValidator
     */
    protected $vatValidator;

    /**
     * @var \Magento\Customer\Service\V1\Data\CustomerBuilder
     */
    protected $customerBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Customer\Helper\Address $customerAddressHelper
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param VatValidator $vatValidator
     * @param \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddressHelper,
        \Magento\Customer\Helper\Data $customerHelper,
        VatValidator $vatValidator,
        \Magento\Customer\Service\V1\Data\CustomerBuilder $customerBuilder
    ) {
        $this->customerHelper = $customerHelper;
        $this->customerAddressHelper = $customerAddressHelper;
        $this->vatValidator = $vatValidator;
        $this->customerBuilder = $customerBuilder;
    }

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     */
    public function dispatch(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Sales\Model\Quote\Address $quoteAddress */
        $quoteAddress = $observer->getQuoteAddress();

        /** @var \Magento\Sales\Model\Quote $quote */
        $quote = $quoteAddress->getQuote();
        $customer = $quote->getCustomer();
        $storeId = $customer->getStoreId();

        if (($customer->getCustomAttribute('disable_auto_group_change')
                && $customer->getCustomAttribute('disable_auto_group_change')->getValue())
            || false == $this->vatValidator->isEnabled($quoteAddress, $storeId)
        ) {
            return;
        }

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();
        $groupId = null;
        if (empty($customerVatNumber) || false == $this->customerHelper->isCountryInEU($customerCountryCode)) {
            $groupId = $customer->getId() ? $this->customerHelper->getDefaultCustomerGroupId(
                $storeId
            ) : \Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID;
        } else {
            // Magento always has to emulate group even if customer uses default billing/shipping address
            $groupId = $this->customerHelper->getCustomerGroupIdBasedOnVatNumber(
                $customerCountryCode,
                $this->vatValidator->validate($quoteAddress, $storeId),
                $storeId
            );
        }

        if ($groupId) {
            $quoteAddress->setPrevQuoteCustomerGroupId($quote->getCustomerGroupId());
            $quote->setCustomerGroupId($groupId);
            $customer = $this->customerBuilder->mergeDataObjectWithArray($customer, ['group_id' => $groupId]);
            $quote->setCustomerData($customer);
        }
    }
}
