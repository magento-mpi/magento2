<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\Sales\Model\Service;

/**
 * Class Quote
 * Quote submit service model
 */
class Quote
{
    /**
     * Quote object
     *
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote;

    /**
     * Quote convert object
     *
     * @var \Magento\Sales\Model\Convert\Quote
     */
    protected $_convertor;

    /**
     * List of additional order attributes which will be added to order before save
     *
     * @var array
     */
    protected $_orderData = [];

    /**
     * Order that may be created during submission
     *
     * @var \Magento\Sales\Model\Order
     */
    protected $_order = null;

    /**
     * If it is true, quote will be inactivate after submitting order or nominal items
     *
     * @var bool
     */
    protected $_shouldInactivateQuote = true;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Framework\DB\TransactionFactory
     */
    protected $_transactionFactory;

    /**
     * Address service
     *
     * @var \Magento\Customer\Api\AddressRepositoryInterface
     */
    protected $addressService;

    /**
     * Search criteria builder
     *
     * @var \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder
     */
    protected $criteriaBuilder;

    /**
     * Filter builder
     *
     * @var \Magento\Framework\Service\V1\Data\FilterBuilder
     */
    protected $filterBuilder;

    /**
     * Account management
     *
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * Customer builder
     *
     * @var \Magento\Customer\Api\Data\CustomerInterfaceBuilder
     */
    protected $customerBuilder;

    /**
     * Address builder
     *
     * @var \Magento\Customer\Api\Data\AddressInterfaceBuilder
     */
    protected $addressBuilder;

    /**
     * Class constructor
     *
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Sales\Model\Quote $quote
     * @param \Magento\Sales\Model\Convert\QuoteFactory $convertQuoteFactory
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Framework\DB\TransactionFactory $transactionFactory
     * @param \Magento\Customer\Api\AddressRepositoryInterface $addressService
     * @param \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $criteriaBuilder
     * @param \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder
     * @param \Magento\Customer\Api\AccountManagementInterface $accountManagement
     * @param \Magento\Customer\Api\Data\CustomerInterfaceBuilder $customerBuilder
     * @param \Magento\Customer\Api\Data\AddressInterfaceBuilder $addressBuilder
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Sales\Model\Quote $quote,
        \Magento\Sales\Model\Convert\QuoteFactory $convertQuoteFactory,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\DB\TransactionFactory $transactionFactory,
        \Magento\Customer\Api\AddressRepositoryInterface $addressService,
        \Magento\Framework\Service\V1\Data\SearchCriteriaBuilder $criteriaBuilder,
        \Magento\Framework\Service\V1\Data\FilterBuilder $filterBuilder,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Customer\Api\Data\CustomerInterfaceBuilder $customerBuilder,
        \Magento\Customer\Api\Data\AddressInterfaceBuilder $addressBuilder
    ) {

        $this->addressService = $addressService;
        $this->criteriaBuilder = $criteriaBuilder;
        $this->filterBuilder = $filterBuilder;
        $this->accountManagement = $accountManagement;
        $this->customerBuilder = $customerBuilder;
        $this->addressBuilder = $addressBuilder;
        $this->_eventManager = $eventManager;
        $this->_quote = $quote;
        $this->_convertor = $convertQuoteFactory->create();
        $this->_customerSession = $customerSession;
        $this->_transactionFactory = $transactionFactory;
    }

    /**
     * Quote convertor declaration
     *
     * @param \Magento\Sales\Model\Convert\Quote $convertor
     * @return $this
     */
    public function setConvertor(\Magento\Sales\Model\Convert\Quote $convertor)
    {
        $this->_convertor = $convertor;
        return $this;
    }

    /**
     * Get assigned quote object
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        return $this->_quote;
    }

    /**
     * Specify additional order data
     *
     * @param array $data
     * @return $this
     */
    public function setOrderData(array $data)
    {
        $this->_orderData = $data;
        return $this;
    }

    /**
     * Submit the quote. Quote submit process will create the order based on quote data
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Exception
     */
    public function submitOrderWithDataObject()
    {
        $this->_deleteNominalItems();
        $this->_validate();
        $quote = $this->_quote;
        $isVirtual = $quote->isVirtual();

        $transaction = $this->_transactionFactory->create();

        $customer = null;
        if (!$quote->getCustomerIsGuest()) {
            $customer = $quote->getCustomer();
            $addresses = $customer->getAddresses();

            if ($customer->getId()) {
                foreach ($addresses as $address) {
                    $this->addressBuilder->mergeDataObjectWithArray($address, ['parent_id' => $customer->getId()]);
                }
            } else { //for new customers
                $this->accountManagement->createAccountWithPasswordHash(
                    $customer,
                    $quote->getPasswordHash()
                );

                $this->criteriaBuilder->addFilter(
                    ['eq' => $this->filterBuilder->setField('parent_id')->setValue($customer->getId())->create()]
                );
                $criteria = $this->criteriaBuilder->create();
                $addresses = $this->addressService->getList($criteria)->getItems();

                //Update quote address information
                foreach ($addresses as $address) {
                    if ($address->isDefaultBilling()) {
                        $quote->getBillingAddress()->setCustomerAddressData($address);
                    } else {
                        if ($address->isDefaultShipping()) {
                            $quote->getShippingAddress()->setCustomerAddressData($address);
                        }
                    }
                }
                if ($quote->getShippingAddress() && $quote->getShippingAddress()->getSameAsBilling()) {
                    $quote->getShippingAddress()->setCustomerAddressData(
                        $quote->getBillingAddress()->getCustomerAddress()
                    );
                }
            }

            $quote->setCustomer($customer)->setCustomerAddressData($addresses);
        }
        $transaction->addObject($quote);

        $quote->reserveOrderId();
        if ($isVirtual) {
            $order = $this->_convertor->addressToOrder($quote->getBillingAddress());
        } else {
            $order = $this->_convertor->addressToOrder($quote->getShippingAddress());
        }
        $order->setBillingAddress($this->_convertor->addressToOrderAddress($quote->getBillingAddress()));
        if ($quote->getBillingAddress()->getCustomerAddress()) {
            $order->getBillingAddress()->setCustomerAddressData($quote->getBillingAddress()->getCustomerAddress());
        }
        if (!$isVirtual) {
            $order->setShippingAddress($this->_convertor->addressToOrderAddress($quote->getShippingAddress()));
            if ($quote->getShippingAddress()->getCustomerAddress()) {
                $order->getShippingAddress()->setCustomerAddressData(
                    $quote->getShippingAddress()->getCustomerAddress()
                );
            }
        }
        $order->setPayment($this->_convertor->paymentToOrderPayment($quote->getPayment()));

        foreach ($this->_orderData as $key => $value) {
            $order->setData($key, $value);
        }

        foreach ($quote->getAllItems() as $item) {
            $orderItem = $this->_convertor->itemToOrderItem($item);
            if ($item->getParentItem()) {
                $orderItem->setParentItem($order->getItemByQuoteItemId($item->getParentItem()->getId()));
            }
            $order->addItem($orderItem);
        }

        if ($customer) {
            $order->setCustomerId($customer->getId());
        }
        $order->setQuote($quote);

        $transaction->addObject($order);

        /**
         * We can use configuration data for declare new order status
         */
        $this->_eventManager->dispatch(
            'sales_model_service_quote_submit_before',
            ['order' => $order, 'quote' => $quote]
        );
        try {
            $order->place();
            $transaction->save();
            $this->_inactivateQuote();
            $this->_eventManager->dispatch(
                'sales_model_service_quote_submit_success',
                ['order' => $order, 'quote' => $quote]
            );
        } catch (\Exception $e) {
            //reset order ID's on exception, because order not saved
            $order->setId(null);
            /** @var $item \Magento\Sales\Model\Order\Item */
            foreach ($order->getItemsCollection() as $item) {
                $item->setOrderId(null);
                $item->setItemId(null);
            }

            $this->_eventManager->dispatch(
                'sales_model_service_quote_submit_failure',
                ['order' => $order, 'quote' => $quote]
            );
            throw $e;
        }
        $this->_order = $order;
        return $order;
    }

    /**
     * Submit nominal items
     *
     * @return void
     */
    public function submitNominalItems()
    {
        $this->_validate();
        $this->_eventManager->dispatch(
            'sales_model_service_quote_submit_nominal_items',
            ['quote' => $this->_quote]
        );
        $this->_inactivateQuote();
        $this->_deleteNominalItems();
    }

    /**
     * Submit all available items
     * All created items will be set to the object
     *
     * @return void
     * @throws \Exception
     */
    public function submitAllWithDataObject()
    {
        // don't allow submitNominalItems() to inactivate quote
        $inactivateQuoteOld = $this->_shouldInactivateQuote;
        $this->_shouldInactivateQuote = false;
        try {
            $this->submitNominalItems();
            $this->_shouldInactivateQuote = $inactivateQuoteOld;
        } catch (\Exception $e) {
            $this->_shouldInactivateQuote = $inactivateQuoteOld;
            throw $e;
        }
        // no need to submit the order if there are no normal items remained
        if (!$this->_quote->getAllVisibleItems()) {
            $this->_inactivateQuote();
            return;
        }
        $this->submitOrderWithDataObject();
    }

    /**
     * Get an order that may had been created during submission
     *
     * @return \Magento\Sales\Model\Order
     */
    public function getOrder()
    {
        return $this->_order;
    }

    /**
     * Inactivate quote
     *
     * @return $this
     */
    protected function _inactivateQuote()
    {
        if ($this->_shouldInactivateQuote) {
            $this->_quote->setIsActive(false);
        }
        return $this;
    }

    /**
     * Validate quote data before converting to order
     *
     * @return $this
     * @throws \Magento\Framework\Model\Exception
     */
    protected function _validate()
    {
        if (!$this->getQuote()->isVirtual()) {
            $address = $this->getQuote()->getShippingAddress();
            $addressValidation = $address->validate();
            if ($addressValidation !== true) {
                throw new \Magento\Framework\Model\Exception(
                    __('Please check the shipping address information. %1', implode(' ', $addressValidation))
                );
            }
            $method = $address->getShippingMethod();
            $rate = $address->getShippingRateByCode($method);
            if (!$this->getQuote()->isVirtual() && (!$method || !$rate)) {
                throw new \Magento\Framework\Model\Exception(__('Please specify a shipping method.'));
            }
        }

        $addressValidation = $this->getQuote()->getBillingAddress()->validate();
        if ($addressValidation !== true) {
            throw new \Magento\Framework\Model\Exception(
                __('Please check the billing address information. %1', implode(' ', $addressValidation))
            );
        }

        if (!$this->getQuote()->getPayment()->getMethod()) {
            throw new \Magento\Framework\Model\Exception(__('Please select a valid payment method.'));
        }

        return $this;
    }

    /**
     * Get rid of all nominal items
     *
     * @return void
     */
    protected function _deleteNominalItems()
    {
        foreach ($this->_quote->getAllVisibleItems() as $item) {
            if ($item->isNominal()) {
                $item->isDeleted(true);
            }
        }
    }
}
