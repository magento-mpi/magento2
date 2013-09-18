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
 *
 * @category   Magento
 * @package    Magento_Sales
 * @author     Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model;

class Observer
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
     * @var \Magento\Catalog\Helper\Data
     */
    protected $_catalogData = null;

    /**
     * Customer address
     *
     * @var \Magento\Customer\Helper\Address
     */
    protected $_customerAddress = null;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core data
     *
     * @var \Magento\Core\Helper\Data
     */
    protected $_coreData = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Core\Model\Event\Manager
     */
    protected $_eventManager = null;

    /**
     * @var Magento_Core_Model_Config
     */
    protected $_coreConfig;

    /**
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Helper\Data $coreData
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Customer\Helper\Address $customerAddress
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param Magento_Core_Model_Config $coreConfig
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Core_Helper_Data $coreData,
        Magento_Customer_Helper_Data $customerData,
        Magento_Customer_Helper_Address $customerAddress,
        Magento_Catalog_Helper_Data $catalogData,
        Magento_Core_Model_Config $coreConfig
    ) {
        $this->_eventManager = $eventManager;
        $this->_coreData = $coreData;
        $this->_customerData = $customerData;
        $this->_customerAddress = $customerAddress;
        $this->_catalogData = $catalogData;
        $this->_coreConfig = $coreConfig;
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function cleanExpiredQuotes($schedule)
    {
        $this->_eventManager->dispatch('clear_expired_quotes_before', array('sales_observer' => $this));

        $lifetimes = $this->_coreConfig->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId=>$lifetime) {
            $lifetime *= 86400;

            /** @var $quotes \Magento\Sales\Model\Resource\Quote\Collection */
            $quotes = \Mage::getModel('Magento\Sales\Model\Quote')->getCollection();

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
     * @return \Magento\Sales\Model\Observer
     */
    public function setExpireQuotesAdditionalFilterFields(array $fields)
    {
        $this->_expireQuotesFilterFields = $fields;
        return $this;
    }

    /**
     * Refresh sales order report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function aggregateSalesReportOrderData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Order')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Refresh sales shipment report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function aggregateSalesReportShipmentData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Shipping')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Refresh sales invoiced report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function aggregateSalesReportInvoicedData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Invoiced')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Refresh sales refunded report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function aggregateSalesReportRefundedData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Refunded')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Refresh bestsellers report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return \Magento\Sales\Model\Observer
     */
    public function aggregateSalesReportBestsellersData($schedule)
    {
        \Mage::app()->getLocale()->emulate(0);
        $currentDate = \Mage::app()->getLocale()->date();
        $date = $currentDate->subHour(25);
        \Mage::getResourceModel('Magento\Sales\Model\Resource\Report\Bestsellers')->aggregate($date);
        \Mage::app()->getLocale()->revert();
        return $this;
    }

    /**
     * Set Quote information about MSRP price enabled
     *
     * @param \Magento\Event\Observer $observer
     */
    public function setQuoteCanApplyMsrp(\Magento\Event\Observer $observer)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
        $quote = $observer->getEvent()->getQuote();

        $canApplyMsrp = false;
        if ($this->_catalogData->isMsrpEnabled()) {
            foreach ($quote->getAllAddresses() as $adddress) {
                if ($adddress->getCanApplyMsrp()) {
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
     * @param \Magento\Event\Observer $observer
     * @return null
     */
    public function addVatRequestParamsOrderComment(\Magento\Event\Observer $observer)
    {
        /** @var $orderInstance \Magento\Sales\Model\Order */
        $orderInstance = $observer->getOrder();
        /** @var $orderAddress \Magento\Sales\Model\Order\Address */
        $orderAddress = $this->_getVatRequiredSalesAddress($orderInstance);
        if (!($orderAddress instanceof \Magento\Sales\Model\Order\Address)) {
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
     * @param \Magento\Core\Model\AbstractModel $salesModel
     * @param \Magento\Core\Model\Store|string|int|null $store
     * @return \Magento\Customer\Model\Address\AbstractAddress|null
     */
    protected function _getVatRequiredSalesAddress($salesModel, $store = null)
    {
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($store);
        $requiredAddress = null;
        switch ($configAddressType) {
            case \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING:
                $requiredAddress = $salesModel->getShippingAddress();
                break;
            default:
                $requiredAddress = $salesModel->getBillingAddress();
        }
        return $requiredAddress;
    }

    /**
     * Retrieve customer address (default billing or default shipping) ID on which tax calculation must be based
     *
     * @param \Magento\Customer\Model\Customer $customer
     * @param \Magento\Core\Model\Store|string|int|null $store
     * @return int|string
     */
    protected function _getVatRequiredCustomerAddress(\Magento\Customer\Model\Customer $customer, $store = null)
    {
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($store);
        $requiredAddress = null;
        switch ($configAddressType) {
            case \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING:
                $requiredAddress = $customer->getDefaultShipping();
                break;
            default:
                $requiredAddress = $customer->getDefaultBilling();
        }
        return $requiredAddress;
    }

    /**
     * Handle customer VAT number if needed on collect_totals_before event of quote address
     *
     * @param \Magento\Event\Observer $observer
     */
    public function changeQuoteCustomerGroupId(\Magento\Event\Observer $observer)
    {
        /** @var $addressHelper \Magento\Customer\Helper\Address */
        $addressHelper = $this->_customerAddress;

        $quoteAddress = $observer->getQuoteAddress();
        $quoteInstance = $quoteAddress->getQuote();
        $customerInstance = $quoteInstance->getCustomer();

        $storeId = $customerInstance->getStore();

        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType($storeId);

        // When VAT is based on billing address then Magento have to handle only billing addresses
        $additionalBillingAddressCondition = ($configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_BILLING)
            ? $configAddressType != $quoteAddress->getAddressType() : false;
        // Handle only addresses that corresponds to VAT configuration
        if (!$addressHelper->isVatValidationEnabled($storeId) || $additionalBillingAddressCondition) {
            return;
        }

        /** @var $customerHelper \Magento\Customer\Helper\Data */
        $customerHelper = $this->_customerData;

        $customerCountryCode = $quoteAddress->getCountryId();
        $customerVatNumber = $quoteAddress->getVatId();

        if (empty($customerVatNumber) || !$this->_coreData->isCountryInEU($customerCountryCode)) {
            $groupId = ($customerInstance->getId()) ? $customerHelper->getDefaultCustomerGroupId($storeId)
                : \Magento\Customer\Model\Group::NOT_LOGGED_IN_ID;

            $quoteAddress->setPrevQuoteCustomerGroupId($quoteInstance->getCustomerGroupId());
            $customerInstance->setGroupId($groupId);
            $quoteInstance->setCustomerGroupId($groupId);

            return;
        }

        /** @var $coreHelper \Magento\Core\Helper\Data */
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
            $gatewayResponse = new \Magento\Object(array(
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
     * @param \Magento\Event\Observer $observer
     */
    public function restoreQuoteCustomerGroupId($observer)
    {
        $quoteAddress = $observer->getQuoteAddress();
        $configAddressType = $this->_customerAddress->getTaxCalculationAddressType();
        // Restore initial customer group ID in quote only if VAT is calculated based on shipping address
        if ($quoteAddress->hasPrevQuoteCustomerGroupId()
            && $configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING
        ) {
            $quoteAddress->getQuote()->setCustomerGroupId($quoteAddress->getPrevQuoteCustomerGroupId());
            $quoteAddress->unsPrevQuoteCustomerGroupId();
        }
    }
}
