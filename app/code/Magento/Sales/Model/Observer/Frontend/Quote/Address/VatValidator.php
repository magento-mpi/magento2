<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\Sales\Model\Observer\Frontend\Quote\Address;

class VatValidator
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
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Customer\Helper\Data $customerData
     */
    public function __construct(
        \Magento\Customer\Helper\Address $customerAddress,
        \Magento\Customer\Helper\Data $customerData
    ) {
        $this->customerData = $customerData;
        $this->customerAddress = $customerAddress;
    }

    /**
     * Validate VAT number
     *
     * @param \Magento\Sales\Model\Quote\Address $quoteAddress
     * @param \Magento\Core\Model\Store $store
     * @return \Magento\Object
     */
    public function validate(\Magento\Sales\Model\Quote\Address $quoteAddress, \Magento\Core\Model\Store $store)
    {
        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();

        $merchantCountryCode = $this->customerData->getMerchantCountryCode();
        $merchantVatNumber = $this->customerData->getMerchantVatNumber();

        $validationResult = null;
        if ($this->customerAddress->getValidateOnEachTransaction($store)
            || $customerCountryCode != $quoteAddress->getValidatedCountryCode()
            || $customerVatNumber != $quoteAddress->getValidatedVatNumber()
        ) {
            // Send request to gateway
            $validationResult = $this->customerData->checkVatNumber(
                $customerCountryCode,
                $customerVatNumber,
                $merchantVatNumber !== '' ? $merchantCountryCode : '',
                $merchantVatNumber
            );

            // Store validation results in corresponding quote address
            $quoteAddress->setVatIsValid((int)$validationResult->getIsValid());
            $quoteAddress->setVatRequestId($validationResult->getRequestIdentifier());
            $quoteAddress->setVatRequestDate($validationResult->getRequestDate());
            $quoteAddress->setVatRequestSuccess($validationResult->getRequestSuccess());
            $quoteAddress->setValidatedVatNumber($customerVatNumber);
            $quoteAddress->setValidatedCountryCode($customerCountryCode);
            $quoteAddress->save();
        } else {
            // Restore validation results from corresponding quote address
            $validationResult = new \Magento\Object(array(
                'is_valid' => (int)$quoteAddress->getVatIsValid(),
                'request_identifier' => (string)$quoteAddress->getVatRequestId(),
                'request_date' => (string)$quoteAddress->getVatRequestDate(),
                'request_success' => (boolean)$quoteAddress->getVatRequestSuccess()
            ));
        }

        return $validationResult;
    }

    /**
     * Check whether VAT ID validation is enabled
     *
     * @param \Magento\Sales\Model\Quote\Address $quoteAddress
     * @param \Magento\Core\Model\Store $store
     * @return bool
     */
    public function isEnabled(\Magento\Sales\Model\Quote\Address $quoteAddress, $store)
    {
        $configAddressType = $this->customerAddress->getTaxCalculationAddressType($store);

        /**
         * TODO: References to Magento\Customer\Model\Address\AbstractAddress will be eliminated
         * in scope of MAGETWO-21105
         */
        // When VAT is based on billing address then Magento have to handle only billing addresses
        $additionalBillingAddressCondition =
            $configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING
                ? $configAddressType != $quoteAddress->getAddressType()
                : false;

        // Handle only addresses that corresponds to VAT configuration
        if (!$this->customerAddress->isVatValidationEnabled($store) || $additionalBillingAddressCondition) {
            return false;
        }

        return true;
    }
}
