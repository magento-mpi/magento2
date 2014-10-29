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
     * @var \Magento\Customer\Api\Data\CustomerDataBuilder
     */
    protected $customerBuilder;

    /**
     * Initialize dependencies.
     *
     * @param \Magento\Customer\Helper\Address $customerAddressHelper
     * @param \Magento\Customer\Helper\Data $customerHelper
     * @param VatValidator $vatValidator
     * @param \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddressHelper,
        \Magento\Customer\Helper\Data $customerHelper,
        VatValidator $vatValidator,
        \Magento\Customer\Api\Data\CustomerDataBuilder $customerBuilder
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
        $customerData = $quote->getCustomerData();
        $storeId = $customerData->getStoreId();

        if (($customerData->getCustomAttribute('disable_auto_group_change')
                && $customerData->getCustomAttribute('disable_auto_group_change')->getValue())
            || false == $this->vatValidator->isEnabled($quoteAddress, $storeId)
        ) {
            return;
        }

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();
        $groupId = null;
        if (empty($customerVatNumber) || false == $this->customerHelper->isCountryInEU($customerCountryCode)) {
            $groupId = $customerData->getId() ? $this->customerHelper->getDefaultCustomerGroupId(
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
            $customerData = $this->customerBuilder->mergeDataObjectWithArray(
                $customerData,
                array('group_id' => $groupId)
            );
            $quote->setCustomerData($customerData);
        }
    }
}
