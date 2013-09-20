<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Sales observer
 */
class Magento_Sales_Model_Observer
{
    /**
     * Expire quotes additional fields to filter
     *
     * @var array
     */
    protected $_expireQuotesFilterFields = array();

    /**
     * Catalog data
     *
     * @var Magento_Catalog_Helper_Data
     */
    protected $_catalogData = null;

    /**
     * Customer address
     *
     * @var Magento_Customer_Helper_Address
     */
    protected $_customerAddress = null;

    /**
     * Customer data
     *
     * @var Magento_Customer_Helper_Data
     */
    protected $_customerData = null;

    /**
     * Core data
     *
     * @var Magento_Core_Helper_Data
     */
    protected $_coreData = null;

    /**
     * Core event manager proxy
     *
     * @var Magento_Core_Model_Event_Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @var Magento_Sales_Model_Resource_Quote_CollectionFactory
     */
    protected $_quoteCollectionFactory;

    /**
     * @var Magento_Core_Model_LocaleInterface
     */
    protected $_coreLocale;

    /**
     * @var Magento_Sales_Model_ResourceFactory
     */
    protected $_resourceFactory;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Core_Helper_Data $coreData
     * @param Magento_Customer_Helper_Data $customerData
     * @param Magento_Customer_Helper_Address $customerAddress
     * @param Magento_Catalog_Helper_Data $catalogData
     * @param Magento_Core_Model_Config $coreConfig
     * @param Magento_Sales_Model_Resource_Quote_CollectionFactory $quoteFactory
     * @param Magento_Core_Model_LocaleInterface $coreLocale
     * @param Magento_Sales_Model_ResourceFactory $resourceFactory
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Customer_Helper_Data $customerData,
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $coreConfig,
        Magento_Sales_Model_Resource_Quote_CollectionFactory $quoteFactory,
        Magento_Core_Model_LocaleInterface $coreLocale,
        Magento_Sales_Model_ResourceFactory $resourceFactory
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_customerData = $customerData;
        $this->_customerAddress = $customerAddress;
        $this->_catalogData = $catalogData;
        $this->_coreConfig = $coreConfig;
        $this->_quoteCollectionFactory = $quoteFactory;
        $this->_coreLocale = $coreLocale;
        $this->_resourceFactory = $resourceFactory;
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function cleanExpiredQuotes($schedule)
    {
        $this->_eventManager->dispatch('clear_expired_quotes_before', array('sales_observer' => $this));

        $lifetimes = $this->_coreConfig->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId=>$lifetime) {
            $lifetime *= 86400;

            /** @var $quotes Magento_Sales_Model_Resource_Quote_Collection */
            $quotes = $this->_quoteCollectionFactory->create();

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('updated_at', array('to'=>date("Y-m-d", time()-$lifetime)));
            $quotes->addFieldToFilter('is_active', 0);

            foreach ($this->getExpireQuotesAdditionalFilterFields() as $field => $condition) {
                $quotes->addFieldToFilter($field, $condition);
            }

            $quotes->walk('delete');
        }
        return $this;
    }

    /**
     * Retrieve expire quotes additional fields to filter
     *
     * @return array
     */
    public function getExpireQuotesAdditionalFilterFields()
    {
        return $this->_expireQuotesFilterFields;
    }

    /**
     * Set expire quotes additional fields to filter
     *
     * @param array $fields
     * @return Magento_Sales_Model_Observer
     */
    public function setExpireQuotesAdditionalFilterFields(array $fields)
    {
        $this->_expireQuotesFilterFields = $fields;
        return $this;
    }

    /**
     * Refresh sales order report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function aggregateSalesReportOrderData($schedule)
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_resourceFactory->create('Magento_Sales_Model_Resource_Report_Order')->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }

    /**
     * Refresh sales shipment report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function aggregateSalesReportShipmentData($schedule)
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_resourceFactory->create('Magento_Sales_Model_Resource_Report_Shipping')->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }

    /**
     * Refresh sales invoiced report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function aggregateSalesReportInvoicedData($schedule)
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_resourceFactory->create('Magento_Sales_Model_Resource_Report_Invoiced')->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }

    /**
     * Refresh sales refunded report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function aggregateSalesReportRefundedData($schedule)
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_resourceFactory->create('Magento_Sales_Model_Resource_Report_Refunded')->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }

    /**
     * Refresh bestsellers report statistics for last day
     *
     * @param Magento_Cron_Model_Schedule $schedule
     * @return Magento_Sales_Model_Observer
     */
    public function aggregateSalesReportBestsellersData($schedule)
    {
        $this->_coreLocale->emulate(0);
        $currentDate = $this->_coreLocale->date();
        $date = $currentDate->subHour(25);
        $this->_resourceFactory->create('Magento_Sales_Model_Resource_Report_Bestsellers')->aggregate($date);
        $this->_coreLocale->revert();
        return $this;
    }

    /**
     * Set Quote information about MSRP price enabled
     *
     * @param Magento_Event_Observer $observer
     */
    public function setQuoteCanApplyMsrp(Magento_Event_Observer $observer)
    {
        /** @var $quote Magento_Sales_Model_Quote */
        $quote = $observer->getEvent()->getQuote();

        $canApplyMsrp = false;
        if ($this->_catalogData->isMsrpEnabled()) {
            foreach ($quote->getAllAddresses() as $address) {
                if ($address->getCanApplyMsrp()) {
                    $canApplyMsrp = true;
                    break;
                }
            }
        }

        $quote->setCanApplyMsrp($canApplyMsrp);
    }

    /**
     * Add VAT validation request date and identifier to order comments
     *
     * @param Magento_Event_Observer $observer
     * @return null
     */
    public function addVatRequestParamsOrderComment(Magento_Event_Observer $observer)
    {
        /** @var $orderInstance Magento_Sales_Model_Order */
        $orderInstance = $observer->getOrder();
        /** @var $orderAddress Magento_Sales_Model_Order_Address */
        $orderAddress = $this->_getVatRequiredSalesAddress($orderInstance);
        if (!($orderAddress instanceof Magento_Sales_Model_Order_Address)) {
            return;
        }

        $vatRequestId = $orderAddress->getVatRequestId();
        $vatRequestDate = $orderAddress->getVatRequestDate();
        if (is_string($vatRequestId) && !empty($vatRequestId) && is_string($vatRequestDate)
            && !empty($vatRequestDate)
        ) {
            $orderHistoryComment = __('VAT Request Identifier')
                . ': ' . $vatRequestId . '<br />' . __('VAT Request Date')
                . ': ' . $vatRequestDate;
            $orderInstance->addStatusHistoryComment($orderHistoryComment, false);
        }
    }

    /**
     * Retrieve sales address (order or quote) on which tax calculation must be based
     *
     * @param Magento_Core_Model_Abstract $salesModel
     * @param Magento_Core_Model_Store|string|int|null $store
     * @return Magento_Customer_Model_Address_Abstract|null
     */
    protected function _getVatRequiredSalesAddress($salesModel, $store = null)
    {
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($store);
        $requiredAddress = null;
        switch ($configAddressType) {
            case Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                $requiredAddress = $salesModel->getShippingAddress();
                break;
            default:
                $requiredAddress = $salesModel->getBillingAddress();
                break;
        }
        return $requiredAddress;
    }

    /**
     * Retrieve customer address (default billing or default shipping) ID on which tax calculation must be based
     *
     * @param Magento_Customer_Model_Customer $customer
     * @param Magento_Core_Model_Store|string|int|null $store
     * @return int|string
     */
    protected function _getVatRequiredCustomerAddress(Magento_Customer_Model_Customer $customer, $store = null)
    {
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($store);
        $requiredAddress = null;
        switch ($configAddressType) {
            case Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING:
                $requiredAddress = $customer->getDefaultShipping();
                break;
            default:
                $requiredAddress = $customer->getDefaultBilling();
                break;
        }
        return $requiredAddress;
    }

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param Magento_Event_Observer $observer
     */
    public function changeQuoteCustomerGroupId(Magento_Event_Observer $observer)
    {
        /** @var $addressHelper Magento_Customer_Helper_Address */
        $addressHelper = $this->_customerAddress;

        $quoteAddress = $observer->getQuoteAddress();
        $quoteInstance = $quoteAddress->getQuote();
        $customerInstance = $quoteInstance->getCustomer();

        $storeId = $customerInstance->getStore();

        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($storeId);

        // When VAT is based on billing address then Magento have to handle only billing addresses
        $additionalBillingAddressCondition = ($configAddressType == Magento_Customer_Model_Address_Abstract::TYPE_BILLING)
            ? $configAddressType != $quoteAddress->getAddressType() : false;
        // Handle only addresses that corresponds to VAT configuration
        if (!$addressHelper->isVatValidationEnabled($storeId) || $additionalBillingAddressCondition) {
            return;
        }

        /** @var $customerHelper Magento_Customer_Helper_Data */
        $customerHelper = $this->_customerData;

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();

        if (empty($customerVatNumber) || !$this->_coreData->isCountryInEU($customerCountryCode)) {
            $groupId = ($customerInstance->getId()) ? $customerHelper->getDefaultCustomerGroupId($storeId)
                : Magento_Customer_Model_Group::NOT_LOGGED_IN_ID;

            $quoteAddress->setPrevQuoteCustomerGroupId($quoteInstance->getCustomerGroupId());
            $customerInstance->setGroupId($groupId);
            $quoteInstance->setCustomerGroupId($groupId);

            return;
        }

        /** @var $coreHelper Magento_Core_Helper_Data */
        $coreHelper = $this->_coreData;
        $merchantCountryCode = $coreHelper->getMerchantCountryCode();
        $merchantVatNumber = $coreHelper->getMerchantVatNumber();

        $gatewayResponse = null;
        if ($addressHelper->getValidateOnEachTransaction($storeId)
            || $customerCountryCode != $quoteAddress->getValidatedCountryCode()
            || $customerVatNumber != $quoteAddress->getValidatedVatNumber()
        ) {
            // Send request to gateway
            $gatewayResponse = $customerHelper->checkVatNumber(
                $customerCountryCode,
                $customerVatNumber,
                ($merchantVatNumber !== '') ? $merchantCountryCode : '',
                $merchantVatNumber
            );

            // Store validation results in corresponding quote address
            $quoteAddress->setVatIsValid((int)$gatewayResponse->getIsValid())
                ->setVatRequestId($gatewayResponse->getRequestIdentifier())
                ->setVatRequestDate($gatewayResponse->getRequestDate())
                ->setVatRequestSuccess($gatewayResponse->getRequestSuccess())
                ->setValidatedVatNumber($customerVatNumber)
                ->setValidatedCountryCode($customerCountryCode)
                ->save();
        } else {
            // Restore validation results from corresponding quote address
            $gatewayResponse = new Magento_Object(array(
                'is_valid' => (int)$quoteAddress->getVatIsValid(),
                'request_identifier' => (string)$quoteAddress->getVatRequestId(),
                'request_date' => (string)$quoteAddress->getVatRequestDate(),
                'request_success' => (boolean)$quoteAddress->getVatRequestSuccess()
            ));
        }

        // Magento always has to emulate group even if customer uses default billing/shipping address
        $groupId = $customerHelper->getCustomerGroupIdBasedOnVatNumber(
            $customerCountryCode, $gatewayResponse, $customerInstance->getStore()
        );

        if ($groupId) {
            $quoteAddress->setPrevQuoteCustomerGroupId($quoteInstance->getCustomerGroupId());
            $customerInstance->setGroupId($groupId);
            $quoteInstance->setCustomerGroupId($groupId);
        }
    }

    /**
     * Restore initial customer group ID in quote if needed on collect_totals_after event of quote address
     *
     * @param Magento_Event_Observer $observer
     */
    public function restoreQuoteCustomerGroupId($observer)
    {
        $quoteAddress = $observer->getQuoteAddress();
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType();
        // Restore initial customer group ID in quote only if VAT is calculated based on shipping address
        if ($quoteAddress->hasPrevQuoteCustomerGroupId()
            && $configAddressType == Magento_Customer_Model_Address_Abstract::TYPE_SHIPPING
        ) {
            $quoteAddress->getQuote()->setCustomerGroupId($quoteAddress->getPrevQuoteCustomerGroupId());
            $quoteAddress->unsPrevQuoteCustomerGroupId();
        }
    }
}
