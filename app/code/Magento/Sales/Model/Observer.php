<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Sales
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model;

/**
 * Sales observer
 */
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
    protected $_customerAddressHelper;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager;

    /**
     * @var \Magento\Core\Model\Store\Config
     */
    protected $_storeConfig;

    /**
     * @var \Magento\Sales\Model\Resource\Quote\CollectionFactory
     */
    protected $_quoteCollectionFactory;

    /**
     * @var \Magento\Stdlib\DateTime\TimezoneInterface
     */
    protected $_localeDate;

    /**
     * @var Resource\Report\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Report\InvoicedFactory
     */
    protected $_invoicedFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Report\RefundedFactory
     */
    protected $_refundedFactory;

    /**
     * @var \Magento\Sales\Model\Resource\Report\BestsellersFactory
     */
    protected $_bestsellersFactory;

    /**
     * @var \Magento\Locale\ResolverInterface
     */
    protected $_localeResolver;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Customer\Helper\Address $customerAddressHelper
     * @param \Magento\Catalog\Helper\Data $catalogData
     * @param \Magento\Core\Model\Store\Config $storeConfig
     * @param \Magento\Sales\Model\Resource\Quote\CollectionFactory $quoteFactory
     * @param \Magento\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param Resource\Report\OrderFactory $orderFactory
     * @param Resource\Report\InvoicedFactory $invoicedFactory
     * @param Resource\Report\RefundedFactory $refundedFactory
     * @param Resource\Report\BestsellersFactory $bestsellersFactory
     * @param \Magento\Locale\ResolverInterface $localeResolver
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Customer\Helper\Address $customerAddressHelper,
        \Magento\Catalog\Helper\Data $catalogData,
        \Magento\Core\Model\Store\Config $storeConfig,
        \Magento\Sales\Model\Resource\Quote\CollectionFactory $quoteFactory,
        \Magento\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Sales\Model\Resource\Report\OrderFactory $orderFactory,
        \Magento\Sales\Model\Resource\Report\InvoicedFactory $invoicedFactory,
        \Magento\Sales\Model\Resource\Report\RefundedFactory $refundedFactory,
        \Magento\Sales\Model\Resource\Report\BestsellersFactory $bestsellersFactory,
        \Magento\Locale\ResolverInterface $localeResolver
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerData = $customerData;
        $this->_customerAddressHelper = $customerAddressHelper;
        $this->_catalogData = $catalogData;
        $this->_storeConfig = $storeConfig;
        $this->_quoteCollectionFactory = $quoteFactory;
        $this->_localeDate = $localeDate;
        $this->_orderFactory = $orderFactory;
        $this->_invoicedFactory = $invoicedFactory;
        $this->_refundedFactory = $refundedFactory;
        $this->_bestsellersFactory = $bestsellersFactory;
        $this->_localeResolver = $localeResolver;
    }

    /**
     * Clean expired quotes (cron process)
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return $this
     */
    public function cleanExpiredQuotes($schedule)
    {
        $this->_eventManager->dispatch('clear_expired_quotes_before', array('sales_observer' => $this));

        $lifetimes = $this->_storeConfig->getStoresConfigByPath('checkout/cart/delete_quote_after');
        foreach ($lifetimes as $storeId => $lifetime) {
            $lifetime *= 86400;

            /** @var $quotes \Magento\Sales\Model\Resource\Quote\Collection */
            $quotes = $this->_quoteCollectionFactory->create();

            $quotes->addFieldToFilter('store_id', $storeId);
            $quotes->addFieldToFilter('updated_at', array('to' => date("Y-m-d", time() - $lifetime)));
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
     * @return $this
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
     * @return $this
     */
    public function aggregateSalesReportOrderData($schedule)
    {
        $this->_localeResolver->emulate(0);
        $currentDate = $this->_localeDate->date();
        $date = $currentDate->subHour(25);
        $this->_orderFactory->create()->aggregate($date);
        $this->_localeResolver->revert();
        return $this;
    }

    /**
     * Refresh sales invoiced report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return $this
     */
    public function aggregateSalesReportInvoicedData($schedule)
    {
        $this->_localeResolver->emulate(0);
        $currentDate = $this->_localeDate->date();
        $date = $currentDate->subHour(25);
        $this->_invoicedFactory->create()->aggregate($date);
        $this->_localeResolver->revert();
        return $this;
    }

    /**
     * Refresh sales refunded report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return $this
     */
    public function aggregateSalesReportRefundedData($schedule)
    {
        $this->_localeResolver->emulate(0);
        $currentDate = $this->_localeDate->date();
        $date = $currentDate->subHour(25);
        $this->_refundedFactory->create()->aggregate($date);
        $this->_localeResolver->revert();
        return $this;
    }

    /**
     * Refresh bestsellers report statistics for last day
     *
     * @param \Magento\Cron\Model\Schedule $schedule
     * @return $this
     */
    public function aggregateSalesReportBestsellersData($schedule)
    {
        $this->_localeResolver->emulate(0);
        $currentDate = $this->_localeDate->date();
        $date = $currentDate->subHour(25);
        $this->_bestsellersFactory->create()->aggregate($date);
        $this->_localeResolver->revert();
        return $this;
    }

    /**
     * Set Quote information about MSRP price enabled
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function setQuoteCanApplyMsrp(\Magento\Event\Observer $observer)
    {
        /** @var $quote \Magento\Sales\Model\Quote */
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
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function addVatRequestParamsOrderComment(\Magento\Event\Observer $observer)
    {
        /** @var $orderInstance \Magento\Sales\Model\Order */
        $orderInstance = $observer->getOrder();
        /** @var $orderAddress \Magento\Sales\Model\Order\Address */
        $orderAddress = $this->_getVatRequiredSalesAddress($orderInstance);
        if (!$orderAddress instanceof \Magento\Sales\Model\Order\Address) {
            return;
        }

        $vatRequestId = $orderAddress->getVatRequestId();
        $vatRequestDate = $orderAddress->getVatRequestDate();
        if (is_string($vatRequestId) && !empty($vatRequestId) && is_string($vatRequestDate) && !empty($vatRequestDate)
        ) {
            $orderHistoryComment = __(
                'VAT Request Identifier'
            ) . ': ' . $vatRequestId . '<br />' . __(
                'VAT Request Date'
            ) . ': ' . $vatRequestDate;
            $orderInstance->addStatusHistoryComment($orderHistoryComment, false);
        }
    }

    /**
     * Retrieve sales address (order or quote) on which tax calculation must be based
     *
     * @param \Magento\Sales\Model\Order $order
     * @param \Magento\Core\Model\Store|string|int|null $store
     * @return \Magento\Sales\Model\Order\Address|null
     */
    protected function _getVatRequiredSalesAddress($order, $store = null)
    {
        $configAddressType = $this->_customerAddressHelper->getTaxCalculationAddressType($store);
        $requiredAddress = null;
        switch ($configAddressType) {
            case \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING:
                $requiredAddress = $order->getShippingAddress();
                break;
            default:
                $requiredAddress = $order->getBillingAddress();
                break;
        }
        return $requiredAddress;
    }

    /**
     * Restore initial customer group ID in quote if needed on collect_totals_after event of quote address
     *
     * @param \Magento\Event\Observer $observer
     * @return void
     */
    public function restoreQuoteCustomerGroupId($observer)
    {
        $quoteAddress = $observer->getQuoteAddress();
        $configAddressType = $this->_customerAddressHelper->getTaxCalculationAddressType();
        // Restore initial customer group ID in quote only if VAT is calculated based on shipping address
        if ($quoteAddress->hasPrevQuoteCustomerGroupId() &&
            $configAddressType == \Magento\Customer\Model\Address\AbstractAddress::TYPE_SHIPPING
        ) {
            $quoteAddress->getQuote()->setCustomerGroupId($quoteAddress->getPrevQuoteCustomerGroupId());
            $quoteAddress->unsPrevQuoteCustomerGroupId();
        }
    }
}
