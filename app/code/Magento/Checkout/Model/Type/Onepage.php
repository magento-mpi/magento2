<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Checkout
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * One page checkout processing model
 */
namespace Magento\Checkout\Model\Type;

use Magento\Customer\Service\V1\Dto\CustomerBuilder;
use Magento\Customer\Service\V1\Dto\AddressBuilder;
use Magento\Customer\Service\V1\Dto\Address as AddressDto;
use Magento\Customer\Service\V1\CustomerGroupServiceInterface;
use Magento\Customer\Model\Metadata\Form;
use Magento\Customer\Service\V1\CustomerAccountServiceInterface;
use Magento\Exception\NoSuchEntityException;
use Magento\Customer\Service\V1\CustomerAddressServiceInterface;
use Magento\Customer\Service\V1\CustomerServiceInterface;
use Magento\Customer\Service\V1\CustomerMetadataServiceInterface as CustomerMetadata;

class Onepage
{
    /**
     * Checkout types: Checkout as Guest, Register, Logged In Customer
     */
    const METHOD_GUEST    = 'guest';
    const METHOD_REGISTER = 'register';
    const METHOD_CUSTOMER = 'customer';

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $_customerSession;

    /**
     * @var \Magento\Checkout\Model\Session
     */
    protected $_checkoutSession;

    /**
     * @var \Magento\Sales\Model\Quote
     */
    protected $_quote = null;

    /**
     * @var \Magento\Checkout\Helper\Data
     */
    protected $_helper;

    /**
     * @var \Magento\Logger
     */
    protected $_logger;

    /**
     * Customer data
     *
     * @var \Magento\Customer\Helper\Data
     */
    protected $_customerData = null;

    /**
     * Core event manager proxy
     *
     * @var \Magento\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Magento\Core\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\App\RequestInterface
     */
    protected $_request;

    /**
     * @var \Magento\Customer\Model\AddressFactory
     */
    protected $_customrAddrFactory;

    /**
     * @var \Magento\Customer\Model\FormFactory
     */
    protected $_customerFormFactory;

    /**
     * @var \Magento\Customer\Model\CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var \Magento\Sales\Model\Service\QuoteFactory
     */
    protected $_serviceQuoteFactory;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Object\Copy
     */
    protected $_objectCopyService;

    /**
     * @var \Magento\Message\ManagerInterface
     */
    protected $messageManager;

    /**
     * @var CustomerAccountServiceInterface
     */
    protected $_accountService;

    /** @var \Magento\Customer\Model\Metadata\FormFactory */
    protected $_formFactory;

    /** @var CustomerBuilder */
    protected $_customerBuilder;

    /** @var AddressBuilder */
    protected $_addressBuilder;

    /** @var \Magento\Math\Random */
    protected $mathRandom;

    /** @var CustomerServiceInterface */
    protected $_customerService;

    /** @var CustomerAddressServiceInterface */
    protected $_customerAddressService;

    /**
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Checkout\Helper\Data $helper
     * @param \Magento\Customer\Helper\Data $customerData
     * @param \Magento\Logger $logger
     * @param \Magento\Checkout\Model\Session $checkoutSession
     * @param \Magento\Customer\Model\Session $customerSession
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\App\RequestInterface $request
     * @param \Magento\Customer\Model\AddressFactory $customrAddrFactory
     * @param \Magento\Customer\Model\FormFactory $customerFormFactory
     * @param \Magento\Customer\Model\CustomerFactory $customerFactory
     * @param \Magento\Sales\Model\Service\QuoteFactory $serviceQuoteFactory
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Object\Copy $objectCopyService
     * @param \Magento\Message\ManagerInterface $messageManager
     * @param CustomerAccountServiceInterface $accountService
     * @param \Magento\Customer\Model\Metadata\FormFactory $formFactory
     * @param CustomerBuilder $customerBuilder
     * @param AddressBuilder $addressBuilder
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Encryption\EncryptorInterface $encryptor
     * @param CustomerServiceInterface $customerService
     * @param CustomerAddressServiceInterface $customerAddressService
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Checkout\Helper\Data $helper,
        \Magento\Customer\Helper\Data $customerData,
        \Magento\Logger $logger,
        \Magento\Checkout\Model\Session $checkoutSession,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\App\RequestInterface $request,
        \Magento\Customer\Model\AddressFactory $customrAddrFactory,
        \Magento\Customer\Model\FormFactory $customerFormFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Sales\Model\Service\QuoteFactory $serviceQuoteFactory,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Object\Copy $objectCopyService,
        \Magento\Message\ManagerInterface $messageManager,
        CustomerAccountServiceInterface $accountService,
        \Magento\Customer\Model\Metadata\FormFactory $formFactory,
        CustomerBuilder $customerBuilder,
        AddressBuilder $addressBuilder,
        \Magento\Math\Random $mathRandom,
        \Magento\Encryption\EncryptorInterface $encryptor,
        CustomerServiceInterface $customerService,
        CustomerAddressServiceInterface $customerAddressService
    ) {
        $this->_eventManager = $eventManager;
        $this->_customerData = $customerData;
        $this->_helper = $helper;
        $this->_checkoutSession = $checkoutSession;
        $this->_customerSession = $customerSession;
        $this->_logger = $logger;
        $this->_storeManager = $storeManager;
        $this->_request = $request;
        $this->_customrAddrFactory = $customrAddrFactory;
        $this->_customerFormFactory = $customerFormFactory;
        $this->_customerFactory = $customerFactory;
        $this->_serviceQuoteFactory = $serviceQuoteFactory;
        $this->_orderFactory = $orderFactory;
        $this->_objectCopyService = $objectCopyService;
        $this->messageManager = $messageManager;
        $this->_accountService = $accountService;
        $this->_formFactory = $formFactory;
        $this->_customerBuilder = $customerBuilder;
        $this->_addressBuilder = $addressBuilder;
        $this->mathRandom = $mathRandom;
        $this->_encryptor = $encryptor;
        $this->_customerService = $customerService;
        $this->_customerAddressService = $customerAddressService;
    }

    /**
     * Get frontend checkout session object
     *
     * @return \Magento\Checkout\Model\Session
     */
    public function getCheckout()
    {
        return $this->_checkoutSession;
    }

    /**
     * Quote object getter
     *
     * @return \Magento\Sales\Model\Quote
     */
    public function getQuote()
    {
        if ($this->_quote === null) {
            return $this->_checkoutSession->getQuote();
        }
        return $this->_quote;
    }

    /**
     * Declare checkout quote instance
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function setQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->_quote = $quote;
        return $this;
    }

    /**
     * Get customer session object
     *
     * @return \Magento\Customer\Model\Session
     */
    public function getCustomerSession()
    {
        return $this->_customerSession;
    }

    /**
     * Initialize quote state to be valid for one page checkout
     *
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    public function initCheckout()
    {
        $checkout = $this->getCheckout();
        $customerSession = $this->getCustomerSession();
        if (is_array($checkout->getStepData())) {
            foreach ($checkout->getStepData() as $step=>$data) {
                if (!($step==='login' || $customerSession->isLoggedIn() && $step==='billing')) {
                    $checkout->setStepData($step, 'allow', false);
                }
            }
        }

        $quote = $this->getQuote();
        if ($quote->isMultipleShippingAddresses()) {
            $quote->removeAllAddresses();
            $quote->save();
        }

        /*
        * want to load the correct customer information by assigning to address
        * instead of just loading from sales/quote_address
        */
        $customer = $customerSession->getCustomerData();
        if ($customer) {
            $quote->assignCustomer($customer);
        }
        return $this;
    }

    /**
     * Get quote checkout method
     *
     * @return string
     */
    public function getCheckoutMethod()
    {
        if ($this->getCustomerSession()->isLoggedIn()) {
            return self::METHOD_CUSTOMER;
        }
        if (!$this->getQuote()->getCheckoutMethod()) {
            if ($this->_helper->isAllowedGuestCheckout($this->getQuote())) {
                $this->getQuote()->setCheckoutMethod(self::METHOD_GUEST);
            } else {
                $this->getQuote()->setCheckoutMethod(self::METHOD_REGISTER);
            }
        }
        return $this->getQuote()->getCheckoutMethod();
    }

    /**
     * Specify checkout method
     *
     * @param   string $method
     * @return  array
     */
    public function saveCheckoutMethod($method)
    {
        if (empty($method)) {
            return array('error' => -1, 'message' => __('Invalid data'));
        }

        $this->getQuote()->setCheckoutMethod($method)->save();
        $this->getCheckout()->setStepData('billing', 'allow', true);
        return array();
    }

    /**
     * Save billing address information to quote
     * This method is called by One Page Checkout JS (AJAX) while saving the billing information.
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  \Magento\Checkout\Model\Type\Onepage
     */
    public function saveBilling($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => __('Invalid data'));
        }

        $address = $this->getQuote()->getBillingAddress();
        $addressForm = $this->_formFactory->create(
            \Magento\Customer\Service\V1\CustomerMetadataServiceInterface::ENTITY_TYPE_ADDRESS,
            'customer_address_edit',
            [],
            Form::IGNORE_INVISIBLE,
            [],
            $this->_request->isAjax()
        );

        if (!empty($customerAddressId)) {
            try {
                $customerAddress = $this->_customerAddressService->getAddressById($customerAddressId);
            } catch (Exception $e) {
                /** Address does not exist */
            }
            if (isset($customerAddress)) {
                if ($customerAddress->getCustomerId() != $this->getQuote()->getCustomerId()) {
                    return array('error' => 1,
                        'message' => __('The customer address is not valid.')
                    );
                }

                $address->importCustomerAddressData($customerAddress)->setSaveInAddressBook(0);
                $addressErrors = $addressForm->validateData($address->getData());
                if ($addressErrors !== true) {
                    return array('error' => 1, 'message' => $addressErrors);
                }
            }
        } else {
            // emulate request object
            $addressData = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => array_values($addressErrors));
            }
            $addressData = $addressForm->compactData($addressData);
            $address->addData($addressData);
            //unset billing address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                if (!isset($data[$attribute->getAttributeCode()])) {
                    $address->setData($attribute->getAttributeCode(), NULL);
                }
            }
            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $this->getQuote()->setBillingAddress($address);
        }

        // validate billing address
        if (($validateRes = $address->validate()) !== true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        if (true !== ($result = $this->_validateCustomerData($data))) {
            return $result;
        } else {
            /** Even though _validateCustomerData should not modify data, it does */
            $address = $this->getQuote()->getBillingAddress();
        }

        if (!$this->getQuote()->getCustomerId() && self::METHOD_REGISTER == $this->getQuote()->getCheckoutMethod()) {
            if ($this->_customerEmailExists($address->getEmail(), $this->_storeManager->getWebsite()->getId())) {
                return array(
                    'error' => 1,
                    'message' => __('There is already a registered customer using this email address. Please log in using this email address or enter a different email address to register your account.')
                );
            }
        }

        if (!$this->getQuote()->isVirtual()) {
            /**
             * Billing address using otions
             */
            $usingCase = isset($data['use_for_shipping']) ? (int)$data['use_for_shipping'] : 0;

            switch ($usingCase) {
                case 0:
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shipping->setSameAsBilling(0);
                    break;
                case 1:
                    $billing = clone $address;
                    $billing->unsAddressId()->unsAddressType();
                    $shipping = $this->getQuote()->getShippingAddress();
                    $shippingMethod = $shipping->getShippingMethod();

                    // Billing address properties that must be always copied to shipping address
                    $requiredBillingAttributes = array('customer_address_id');

                    // don't reset original shipping data, if it was not changed by customer
                    foreach ($shipping->getData() as $shippingKey => $shippingValue) {
                        if (!is_null($shippingValue) && !is_null($billing->getData($shippingKey))
                            && !isset($data[$shippingKey]) && !in_array($shippingKey, $requiredBillingAttributes)
                        ) {
                            $billing->unsetData($shippingKey);
                        }
                    }
                    $shipping->addData($billing->getData())
                        ->setSameAsBilling(1)
                        ->setSaveInAddressBook(0)
                        ->setShippingMethod($shippingMethod)
                        ->setCollectShippingRates(true);
                    $this->getCheckout()->setStepData('shipping', 'complete', true);
                    break;
            }
        }

        $this->getQuote()->collectTotals();
        $this->getQuote()->save();

        if (!$this->getQuote()->isVirtual() && $this->getCheckout()->getStepData('shipping', 'complete') == true) {
            //Recollect Shipping rates for shipping methods
            $this->getQuote()->getShippingAddress()->setCollectShippingRates(true);
        }

        $this->getCheckout()
            ->setStepData('billing', 'allow', true)
            ->setStepData('billing', 'complete', true)
            ->setStepData('shipping', 'allow', true);

        return array();
    }

    /**
     * Validate customer data and set some its data for further usage in quote
     *
     * Will return either true or array with error messages
     *
     * @param array $data
     * @return bool|array
     */
    protected function _validateCustomerData(array $data)
    {
        $quote = $this->getQuote();
        $isCustomerNew = !$quote->getCustomerId();
        $customer = $quote->getCustomerData();
        $customerData = $customer->__toArray();

        /** @var Form $customerForm */
        $customerForm = $this->_formFactory->create(
            CustomerMetadata::ENTITY_TYPE_CUSTOMER,
            'checkout_register',
            $customerData,
            Form::IGNORE_INVISIBLE,
            [],
            $this->_request->isAjax()
        );

        if ($isCustomerNew) {
            $customerRequest = $customerForm->prepareRequest($data);
            $customerData = $customerForm->extractData($customerRequest);
        }

        $customerErrors = $customerForm->validateData($customerData);
        if ($customerErrors !== true) {
            return array(
                'error'     => -1,
                'message'   => implode(', ', $customerErrors)
            );
        }

        if (!$isCustomerNew) {
            return true;
        }

        $this->_customerBuilder->populateWithArray($customerData);
        $customer = $this->_customerBuilder->create();

        if ($quote->getCheckoutMethod() == self::METHOD_REGISTER) {
            // We always have $customerRequest here, otherwise we would have been kicked off the function several
            // lines above
            if ($customerRequest->getParam('customer_password') != $customerRequest->getParam('confirm_password')) {
                return array(
                    'error'   => -1,
                    'message' => __('Password and password confirmation are not equal.')
                );
            }
        } else {
            // set NOT LOGGED IN group id explicitly,
            // otherwise copyFieldsetToTarget('customer_account', 'to_quote') will fill it with default group id value
            $this->_customerBuilder->populate($customer);
            $this->_customerBuilder->setGroupId(CustomerGroupServiceInterface::NOT_LOGGED_IN_ID);
            $customer = $this->_customerBuilder->create();
        }

        //validate customer
        $attributes = $customerForm->getAllowedAttributes();
        $result = $this->_accountService->validateCustomerData($customer, $attributes);
        if (true !== $result && is_array($result)) {
            return array(
                'error'   => -1,
                'message' => implode(', ', $result)
            );
        }

        // copy customer/guest email to address
        $quote->getBillingAddress()->setEmail($customer->getEmail());

        // copy customer data to quote
        $this->_objectCopyService->copyFieldsetToTarget('customer_account', 'to_quote', $customer->__toArray(), $quote);

        return true;
    }

    /**
     * Save checkout shipping address
     *
     * @param   array $data
     * @param   int $customerAddressId
     * @return  \Magento\Checkout\Model\Type\Onepage
     */
    public function saveShipping($data, $customerAddressId)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => __('Invalid data'));
        }
        $address = $this->getQuote()->getShippingAddress();

        $addressForm  = $this->_formFactory->create(
            'customer_address',
            'customer_address_edit',
            [],
            Form::IGNORE_INVISIBLE,
            [],
            $this->_request->isAjax()
        );

        if (!empty($customerAddressId)) {
            $addressData = null;
            try {
                $addressData = $this->_customerAddressService->getAddressById($customerAddressId);
            } catch (NoSuchEntityException $e) {
                // do nothing if customer is not found by id
            }

            if ($addressData->getCustomerId() != $this->getQuote()->getCustomerId()) {
                return array('error' => 1,
                    'message' => __('The customer address is not valid.')
                );
            }

            $address->importCustomerAddressData($addressData)->setSaveInAddressBook(0);
            $addressErrors  = $addressForm->validateData($address->getData());
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }

        } else {
            // emulate request object
            $addressData    = $addressForm->extractData($addressForm->prepareRequest($data));
            $addressErrors  = $addressForm->validateData($addressData);
            if ($addressErrors !== true) {
                return array('error' => 1, 'message' => $addressErrors);
            }
            $compactedData = $addressForm->compactData($addressData);
            // unset shipping address attributes which were not shown in form
            foreach ($addressForm->getAttributes() as $attribute) {
                $attributeCode = $attribute->getAttributeCode();
                if (!isset($data[$attributeCode])) {
                    $address->setData($attributeCode, NULL);
                } else {
                    $address->setDataUsingMethod($attributeCode, $compactedData[$attributeCode]);
                }
            }

            $address->setCustomerAddressId(null);
            // Additional form data, not fetched by extractData (as it fetches only attributes)
            $address->setSaveInAddressBook(empty($data['save_in_address_book']) ? 0 : 1);
            $address->setSameAsBilling(empty($data['same_as_billing']) ? 0 : 1);
        }

        $address->setCollectShippingRates(true);

        if (($validateRes = $address->validate())!==true) {
            return array('error' => 1, 'message' => $validateRes);
        }

        $this->getQuote()->collectTotals()->save();

        $this->getCheckout()
            ->setStepData('shipping', 'complete', true)
            ->setStepData('shipping_method', 'allow', true);

        return array();
    }

    /**
     * Specify quote shipping method
     *
     * @param   string $shippingMethod
     * @return  array
     */
    public function saveShippingMethod($shippingMethod)
    {
        if (empty($shippingMethod)) {
            return array('error' => -1, 'message' => __('Invalid shipping method'));
        }
        $rate = $this->getQuote()->getShippingAddress()->getShippingRateByCode($shippingMethod);
        if (!$rate) {
            return array('error' => -1, 'message' => __('Invalid shipping method'));
        }
        $this->getQuote()->getShippingAddress()
            ->setShippingMethod($shippingMethod);

        $this->getCheckout()
            ->setStepData('shipping_method', 'complete', true)
            ->setStepData('payment', 'allow', true);

        return array();
    }

    /**
     * Specify quote payment method
     *
     * @param   array $data
     * @return  array
     */
    public function savePayment($data)
    {
        if (empty($data)) {
            return array('error' => -1, 'message' => __('Invalid data'));
        }
        $quote = $this->getQuote();
        if ($quote->isVirtual()) {
            $quote->getBillingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        } else {
            $quote->getShippingAddress()->setPaymentMethod(isset($data['method']) ? $data['method'] : null);
        }

        // shipping totals may be affected by payment method
        if (!$quote->isVirtual() && $quote->getShippingAddress()) {
            $quote->getShippingAddress()->setCollectShippingRates(true);
        }

        $data['checks'] = \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_CHECKOUT
            | \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_COUNTRY
            | \Magento\Payment\Model\Method\AbstractMethod::CHECK_USE_FOR_CURRENCY
            | \Magento\Payment\Model\Method\AbstractMethod::CHECK_ORDER_TOTAL_MIN_MAX
            | \Magento\Payment\Model\Method\AbstractMethod::CHECK_ZERO_TOTAL;

        $payment = $quote->getPayment();
        $payment->importData($data);

        $quote->save();

        $this->getCheckout()
            ->setStepData('payment', 'complete', true)
            ->setStepData('review', 'allow', true);

        return array();
    }

    /**
     * Validate quote state to be integrated with one page checkout process
     *
     * @throws \Magento\Core\Exception
     */
    protected function validate()
    {
        $quote = $this->getQuote();

        if ($quote->isMultipleShippingAddresses()) {
            throw new \Magento\Core\Exception(__('There are more than one shipping address.'));
        }

        if ($quote->getCheckoutMethod() == self::METHOD_GUEST
            && !$this->_helper->isAllowedGuestCheckout($quote)
        ) {
            throw new \Magento\Core\Exception(__('Sorry, guest checkout is not enabled.'));
        }
    }

    /**
     * Prepare quote for guest checkout order submit
     *
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    protected function _prepareGuestQuote()
    {
        $quote = $this->getQuote();
        $quote->setCustomerId(null)
            ->setCustomerEmail($quote->getBillingAddress()->getEmail())
            ->setCustomerIsGuest(true)
            ->setCustomerGroupId(\Magento\Customer\Service\V1\CustomerGroupServiceInterface::NOT_LOGGED_IN_ID);
        return $this;
    }

    /**
     * Prepare quote for customer registration and customer order submit
     *
     * @return \Magento\Checkout\Model\Type\Onepage
     */
    protected function _prepareNewCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customerData = $quote->getCustomerData();
        // Need to set proper attribute id or future updates will cause data loss.
        $customerData = $this->_customerBuilder->mergeDtoWithArray(
            $customerData,
            [CustomerMetadata::ATTRIBUTE_SET_ID_CUSTOMER => 1]
        );

        $customerBillingData = $billing->exportCustomerAddressData();
        $customerBillingData = $this->_addressBuilder->mergeDtoWithArray(
            $customerBillingData,
            [AddressDto::KEY_DEFAULT_BILLING => true]
        );

        if ($shipping) {
            if( !$shipping->getSameAsBilling()) {
                $customerShippingData = $shipping->exportCustomerAddressData();
                $customerShippingData = $this->_addressBuilder->populate($customerShippingData)
                    ->setDefaultShipping(true)->create();
                $shipping->setCustomerAddress($customerShippingData);
                // Add shipping address to quote since customer DTO does not hold address information
                $quote->addCustomerAddressData($customerShippingData);
            } else {
                $shipping->setCustomerAddressData($customerBillingData);
                $customerBillingData = $this->_addressBuilder->populate($customerBillingData)->setDefaultShipping(true)
                    ->create();
            }
        } else {
            $customerBillingData = $this->_addressBuilder->populate($customerBillingData)->setDefaultShipping(true)
                ->create();
        }
        $billing->setCustomerAddressData($customerBillingData);

        $dataArray = $this->_objectCopyService->getDataFromFieldset(
            'checkout_onepage_quote',
            'to_customer',
            $quote
        );
        $customerData = $this->_customerBuilder->mergeDtoWithArray(
            $customerData,
            $dataArray
        );
        $quote->setCustomerData($customerData)
            ->setCustomerId(true); // TODO : Eventually need to remove this legacy hack
        // Add billing address to quote since customer DTO does not hold address information
        $quote->addCustomerAddressData($customerBillingData);
    }

    /**
     * Prepare quote for customer order submit
     *
     * @return void
     */
    protected function _prepareCustomerQuote()
    {
        $quote      = $this->getQuote();
        $billing    = $quote->getBillingAddress();
        $shipping   = $quote->isVirtual() ? null : $quote->getShippingAddress();

        $customer = $this->_customerService->getCustomer($this->getCustomerSession()->getCustomerId());
        if (!$billing->getCustomerId() || $billing->getSaveInAddressBook()) {
            $billingAddress = $billing->exportCustomerAddressData();
            $billing->setCustomerAddressData($billingAddress);
        }
        if ($shipping && !$shipping->getSameAsBilling() &&
            (!$shipping->getCustomerId() || $shipping->getSaveInAddressBook())) {
            $shippingAddress = $shipping->exportCustomerAddressData();
            $shipping->setCustomerAddressData($shippingAddress);
        }

        if (isset($billingAddress)) {
            if (!$customer->getDefaultBilling() || !$customer->getDefaultShipping()) {
                $billingAddress = $this->_addressBuilder
                    ->populate($billingAddress)
                    ->setDefaultBilling(!$customer->getDefaultBilling())
                    ->setDefaultShipping(!$customer->getDefaultShipping())
                    ->create();
            }

            $quote->addCustomerAddressData($billingAddress);
        }

        if ($shipping && isset($shippingAddress) && !$customer->getDefaultShipping()) {
            $shippingAddress = $this->_addressBuilder
                ->mergeDtoWithArray($shippingAddress, [AddressDto::KEY_DEFAULT_SHIPPING => true]);
            $quote->addCustomerAddressData($shippingAddress);
        }
    }

    /**
     * Involve new customer to system
     *
     * @return $this
     */
    protected function _involveNewCustomer()
    {
        $customer = $this->getQuote()->getCustomerData();
        $confirmationStatus = $this->_accountService->getConfirmationStatus($customer->getCustomerId());
        if ($confirmationStatus === CustomerAccountServiceInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
            $url = $this->_customerData->getEmailConfirmationUrl($customer->getEmail());
            $this->messageManager->addSuccess(
                __('Account confirmation is required. Please, check your e-mail for confirmation link. To resend confirmation email please <a href="%1">click here</a>.', $url)
            );
        } else {
            $this->getCustomerSession()->loginById($customer->getCustomerId());
        }
        return $this;
    }

    /**
     * Create order based on checkout type. Create customer if necessary.
     *
     * @return $this
     */
    public function saveOrder()
    {
        $this->validate();
        $isNewCustomer = false;
        switch ($this->getCheckoutMethod()) {
            case self::METHOD_GUEST:
                $this->_prepareGuestQuote();
                break;
            case self::METHOD_REGISTER:
                $this->_prepareNewCustomerQuote();
                $isNewCustomer = true;
                break;
            default:
                $this->_prepareCustomerQuote();
                break;
        }

        /** @var \Magento\Sales\Model\Service\Quote $quoteService */
        $quoteService = $this->_serviceQuoteFactory->create(['quote' => $this->getQuote()]);
        $quoteService->submitAllWithDto();

        if ($isNewCustomer) {
            try {
                $this->_involveNewCustomer();
            } catch (\Exception $e) {
                $this->_logger->logException($e);
            }
        }

        $this->_checkoutSession->setLastQuoteId($this->getQuote()->getId())
            ->setLastSuccessQuoteId($this->getQuote()->getId())
            ->clearHelperData();

        $order = $quoteService->getOrder();
        if ($order) {
            $this->_eventManager->dispatch(
                'checkout_type_onepage_save_order_after',
                ['order' => $order, 'quote' => $this->getQuote()]
            );

            /**
             * a flag to set that there will be redirect to third party after confirmation
             * eg: paypal standard ipn
             */
            $redirectUrl = $this->getQuote()->getPayment()->getOrderPlaceRedirectUrl();
            /**
             * we only want to send to customer about new order when there is no redirect to third party
             */
            if (!$redirectUrl && $order->getCanSendNewEmailFlag()) {
                try {
                    $order->sendNewOrderEmail();
                } catch (\Exception $e) {
                    $this->_logger->logException($e);
                }
            }

            // add order information to the session
            $this->_checkoutSession->setLastOrderId($order->getId())
                ->setRedirectUrl($redirectUrl)
                ->setLastRealOrderId($order->getIncrementId());
        }

        // add recurring profiles information to the session
        $profiles = $quoteService->getRecurringPaymentProfiles();
        if ($profiles) {
            $ids = [];
            foreach ($profiles as $profile) {
                $ids[] = $profile->getId();
            }
            $this->_checkoutSession->setLastRecurringProfileIds($ids);
            // TODO: send recurring profile emails
        }

        $this->_eventManager->dispatch(
            'checkout_submit_all_after',
            ['order' => $order, 'quote' => $this->getQuote(), 'recurring_profiles' => $profiles]
        );

        return $this;
    }

    /**
     * Check if customer email exists
     *
     * @param string $email
     * @param int $websiteId
     * @return false|\Magento\Customer\Model\Customer
     */
    protected function _customerEmailExists($email, $websiteId = null)
    {
        try {
            $this->_customerService->getCustomerByEmail($email, $websiteId);
            return true;
        } catch (\Exception $e) {
            /** Customer email does not exist */
            return false;
        }
    }

    /**
     * Get last order increment id by order id
     *
     * @return string
     */
    public function getLastOrderId()
    {
        $lastId  = $this->getCheckout()->getLastOrderId();
        $orderId = false;
        if ($lastId) {
            $order = $this->_orderFactory->create();
            $order->load($lastId);
            $orderId = $order->getIncrementId();
        }
        return $orderId;
    }
}
