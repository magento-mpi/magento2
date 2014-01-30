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
 * Sales implementation of recurring payment profiles
 * Implements saving and managing profiles
 *
 * @method \Magento\RecurringProfile\Model\Resource\Profile _getResource()
 * @method \Magento\RecurringProfile\Model\Resource\Profile getResource()
 * @method string getState()
 * @method \Magento\RecurringProfile\Model\Profile setState(string $value)
 * @method int getCustomerId()
 * @method \Magento\RecurringProfile\Model\Profile setCustomerId(int $value)
 * @method int getStoreId()
 * @method \Magento\RecurringProfile\Model\Profile setStoreId(int $value)
 * @method string getMethodCode()
 * @method \Magento\RecurringProfile\Model\Profile setMethodCode(string $value)
 * @method string getCreatedAt()
 * @method \Magento\RecurringProfile\Model\Profile setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\RecurringProfile\Model\Profile setUpdatedAt(string $value)
 * @method string getReferenceId()
 * @method \Magento\RecurringProfile\Model\Profile setReferenceId(string $value)
 * @method string getSubscriberName()
 * @method \Magento\RecurringProfile\Model\Profile setSubscriberName(string $value)
 * @method string getStartDatetime()
 * @method \Magento\RecurringProfile\Model\Profile setStartDatetime(string $value)
 * @method string getInternalReferenceId()
 * @method \Magento\RecurringProfile\Model\Profile setInternalReferenceId(string $value)
 * @method string getScheduleDescription()
 * @method \Magento\RecurringProfile\Model\Profile setScheduleDescription(string $value)
 * @method int getSuspensionThreshold()
 * @method \Magento\RecurringProfile\Model\Profile setSuspensionThreshold(int $value)
 * @method int getBillFailedLater()
 * @method \Magento\RecurringProfile\Model\Profile setBillFailedLater(int $value)
 * @method string getPeriodUnit()
 * @method \Magento\RecurringProfile\Model\Profile setPeriodUnit(string $value)
 * @method int getPeriodFrequency()
 * @method \Magento\RecurringProfile\Model\Profile setPeriodFrequency(int $value)
 * @method int getPeriodMaxCycles()
 * @method \Magento\RecurringProfile\Model\Profile setPeriodMaxCycles(int $value)
 * @method float getBillingAmount()
 * @method \Magento\RecurringProfile\Model\Profile setBillingAmount(float $value)
 * @method string getTrialPeriodUnit()
 * @method \Magento\RecurringProfile\Model\Profile setTrialPeriodUnit(string $value)
 * @method int getTrialPeriodFrequency()
 * @method \Magento\RecurringProfile\Model\Profile setTrialPeriodFrequency(int $value)
 * @method int getTrialPeriodMaxCycles()
 * @method \Magento\RecurringProfile\Model\Profile setTrialPeriodMaxCycles(int $value)
 * @method float getTrialBillingAmount()
 * @method \Magento\RecurringProfile\Model\Profile setTrialBillingAmount(float $value)
 * @method string getCurrencyCode()
 * @method \Magento\RecurringProfile\Model\Profile setCurrencyCode(string $value)
 * @method float getShippingAmount()
 * @method \Magento\RecurringProfile\Model\Profile setShippingAmount(float $value)
 * @method float getTaxAmount()
 * @method \Magento\RecurringProfile\Model\Profile setTaxAmount(float $value)
 * @method float getInitAmount()
 * @method \Magento\RecurringProfile\Model\Profile setInitAmount(float $value)
 * @method int getInitMayFail()
 * @method \Magento\RecurringProfile\Model\Profile setInitMayFail(int $value)
 * @method string getOrderInfo()
 * @method \Magento\RecurringProfile\Model\Profile setOrderInfo(string $value)
 * @method string getOrderItemInfo()
 * @method \Magento\RecurringProfile\Model\Profile setOrderItemInfo(string $value)
 * @method string getBillingAddressInfo()
 * @method \Magento\RecurringProfile\Model\Profile setBillingAddressInfo(string $value)
 * @method string getShippingAddressInfo()
 * @method \Magento\RecurringProfile\Model\Profile setShippingAddressInfo(string $value)
 * @method string getProfileVendorInfo()
 * @method \Magento\RecurringProfile\Model\Profile setProfileVendorInfo(string $value)
 * @method string getAdditionalInfo()
 * @method \Magento\RecurringProfile\Model\Profile setAdditionalInfo(string $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\RecurringProfile\Model;

class Profile extends \Magento\RecurringProfile\Model\RecurringProfile
{
    /**
     * Payment types
     *
     * @var string
     */
    const PAYMENT_TYPE_REGULAR   = 'regular';
    const PAYMENT_TYPE_TRIAL     = 'trial';
    const PAYMENT_TYPE_INITIAL   = 'initial';

    /**
     * Allowed actions matrix
     *
     * @var array
     */
    protected $_workflow = null;

    /**
     * @var \Magento\Sales\Model\OrderFactory
     */
    protected $_orderFactory;

    /**
     * @var \Magento\Sales\Model\Order\AddressFactory
     */
    protected $_addressFactory;

    /**
     * @var \Magento\Sales\Model\Order\PaymentFactory
     */
    protected $_paymentFactory;

    /**
     * @var \Magento\Sales\Model\Order\ItemFactory
     */
    protected $_orderItemFactory;

    /**
     * @var \Magento\Math\Random
     */
    protected $mathRandom;

    /**
     * @var States
     */
    protected $states;

    /**
     * @param \Magento\Core\Model\Context $context
     * @param \Magento\Core\Model\Registry $registry
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param PeriodUnits $periodUnits
     * @param \Magento\RecurringProfile\Block\Fields $fields
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Sales\Model\OrderFactory $orderFactory
     * @param \Magento\Sales\Model\Order\AddressFactory $addressFactory
     * @param \Magento\Sales\Model\Order\PaymentFactory $paymentFactory
     * @param \Magento\Sales\Model\Order\ItemFactory $orderItemFactory
     * @param \Magento\Math\Random $mathRandom
     * @param States $states
     * @param \Magento\Core\Model\Resource\AbstractResource $resource
     * @param \Magento\Data\Collection\Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        \Magento\Core\Model\Context $context,
        \Magento\Core\Model\Registry $registry,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\RecurringProfile\Model\PeriodUnits $periodUnits,
        \Magento\RecurringProfile\Block\Fields $fields,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Sales\Model\OrderFactory $orderFactory,
        \Magento\Sales\Model\Order\AddressFactory $addressFactory,
        \Magento\Sales\Model\Order\PaymentFactory $paymentFactory,
        \Magento\Sales\Model\Order\ItemFactory $orderItemFactory,
        \Magento\Math\Random $mathRandom,
        States $states,
        \Magento\Core\Model\Resource\AbstractResource $resource = null,
        \Magento\Data\Collection\Db $resourceCollection = null,
        array $data = array()
    ) {
        $this->_orderFactory = $orderFactory;
        $this->_addressFactory = $addressFactory;
        $this->_paymentFactory = $paymentFactory;
        $this->_orderItemFactory = $orderItemFactory;
        $this->mathRandom = $mathRandom;
        $this->states = $states;
        parent::__construct(
            $context,
            $registry,
            $paymentData,
            $periodUnits,
            $fields,
            $locale,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Load order by system increment identifier
     *
     * @param string $internalReferenceId
     * @return \Magento\Sales\Model\Order
     */
    public function loadByInternalReferenceId($internalReferenceId)
    {
        return $this->load($internalReferenceId, 'internal_reference_id');
    }

    /**
     * Submit a recurring profile right after an order is placed
     *
     */
    public function submit()
    {
        $this->_getResource()->beginTransaction();
        try {
            $this->setInternalReferenceId($this->mathRandom->getUniqueHash('temporary-'));
            $this->save();
            $this->setInternalReferenceId($this->mathRandom->getUniqueHash($this->getId() . '-'));
            $this->getMethodInstance()->submitRecurringProfile($this, $this->getQuote()->getPayment());
            $this->save();
            $this->_getResource()->commit();
        } catch (\Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
    }

    /**
     * Activate the suspended profile
     */
    public function activate()
    {
        $this->_checkWorkflow(States::STATE_ACTIVE, false);
        $this->setNewState(States::STATE_ACTIVE);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(States::STATE_ACTIVE)
            ->save();
    }

    /**
     * Check whether the workflow allows to activate the profile
     *
     * @return bool
     */
    public function canActivate()
    {
        return $this->_checkWorkflow(States::STATE_ACTIVE);
    }

    /**
     * Suspend active profile
     */
    public function suspend()
    {
        $this->_checkWorkflow(States::STATE_SUSPENDED, false);
        $this->setNewState(States::STATE_SUSPENDED);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(States::STATE_SUSPENDED)
            ->save();
    }

    /**
     * Check whether the workflow allows to suspend the profile
     *
     * @return bool
     */
    public function canSuspend()
    {
        return $this->_checkWorkflow(States::STATE_SUSPENDED);
    }

    /**
     * Cancel active or suspended profile
     */
    public function cancel()
    {
        $this->_checkWorkflow(States::STATE_CANCELED, false);
        $this->setNewState(States::STATE_CANCELED);
        $this->getMethodInstance()->updateRecurringProfileStatus($this);
        $this->setState(States::STATE_CANCELED)->save();
    }

    /**
     * Check whether the workflow allows to cancel the profile
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->_checkWorkflow(States::STATE_CANCELED);
    }

    public function fetchUpdate()
    {
        $result = new \Magento\Object();
        $this->getMethodInstance()->getRecurringProfileDetails($this->getReferenceId(), $result);

        if ($result->getIsProfileActive()) {
            $this->setState(States::STATE_ACTIVE);
        } elseif ($result->getIsProfilePending()) {
            $this->setState(States::STATE_PENDING);
        } elseif ($result->getIsProfileCanceled()) {
            $this->setState(States::STATE_CANCELED);
        } elseif ($result->getIsProfileSuspended()) {
            $this->setState(States::STATE_SUSPENDED);
        } elseif ($result->getIsProfileExpired()) {
            $this->setState(States::STATE_EXPIRED);
        }
    }

    public function canFetchUpdate()
    {
        return $this->getMethodInstance()->canGetRecurringProfileDetails();
    }

    /**
     * Initialize new order based on profile data
     *
     * Takes arbitrary number of \Magento\Object instances to be treated as items for new order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function createOrder()
    {
        $items = array();
        $itemInfoObjects = func_get_args();

        $billingAmount = 0;
        $shippingAmount = 0;
        $taxAmount = 0;
        $isVirtual = 1;
        $weight = 0;
        foreach ($itemInfoObjects as $itemInfo) {
            $item = $this->_getItem($itemInfo);
            $billingAmount += $item->getPrice();
            $shippingAmount += $item->getShippingAmount();
            $taxAmount += $item->getTaxAmount();
            $weight += $item->getWeight();
            if (!$item->getIsVirtual()) {
                $isVirtual = 0;
            }
            $items[] = $item;
        }
        $grandTotal = $billingAmount + $shippingAmount + $taxAmount;

        $order = $this->_orderFactory->create();

        $billingAddress = $this->_addressFactory->create()
            ->setData($this->getBillingAddressInfo())
            ->setId(null);

        $shippingInfo = $this->getShippingAddressInfo();
        $shippingAddress = $this->_addressFactory->create()
            ->setData($shippingInfo)
            ->setId(null);

        $payment = $this->_paymentFactory->create()
            ->setMethod($this->getMethodCode());

        $transferDataKeys = array(
            'store_id',             'store_name',           'customer_id',          'customer_email',
            'customer_firstname',   'customer_lastname',    'customer_middlename',  'customer_prefix',
            'customer_suffix',      'customer_taxvat',      'customer_gender',      'customer_is_guest',
            'customer_note_notify', 'customer_group_id',    'customer_note',        'shipping_method',
            'shipping_description', 'base_currency_code',   'global_currency_code', 'order_currency_code',
            'store_currency_code',  'base_to_global_rate',  'base_to_order_rate',   'store_to_base_rate',
            'store_to_order_rate'
        );

        $orderInfo = $this->getOrderInfo();
        foreach ($transferDataKeys as $key) {
            if (isset($orderInfo[$key])) {
                $order->setData($key, $orderInfo[$key]);
            } elseif (isset($shippingInfo[$key])) {
                $order->setData($key, $shippingInfo[$key]);
            }
        }

        $order->setStoreId($this->getStoreId())
            ->setState(\Magento\Sales\Model\Order::STATE_NEW)
            ->setBaseToOrderRate($this->getInfoValue('order_info', 'base_to_quote_rate'))
            ->setStoreToOrderRate($this->getInfoValue('order_info', 'store_to_quote_rate'))
            ->setOrderCurrencyCode($this->getInfoValue('order_info', 'quote_currency_code'))
            ->setBaseSubtotal($billingAmount)
            ->setSubtotal($billingAmount)
            ->setBaseShippingAmount($shippingAmount)
            ->setShippingAmount($shippingAmount)
            ->setBaseTaxAmount($taxAmount)
            ->setTaxAmount($taxAmount)
            ->setBaseGrandTotal($grandTotal)
            ->setGrandTotal($grandTotal)
            ->setIsVirtual($isVirtual)
            ->setWeight($weight)
            ->setTotalQtyOrdered($this->getInfoValue('order_info', 'items_qty'))
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->setPayment($payment);

        foreach ($items as $item) {
            $order->addItem($item);
        }

        return $order;
    }

    /**
     * Validate states
     *
     * @return bool
     */
    public function isValid()
    {
        parent::isValid();

        // state
        if (!array_key_exists($this->getState(), $this->states->toOptionArray())) {
            $this->_errors['state'][] = __('Wrong state: "%1"', $this->getState());
        }

        return empty($this->_errors);
    }

    /**
     * Import quote information to the profile
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return \Magento\RecurringProfile\Model\Profile
     */
    public function importQuote(\Magento\Sales\Model\Quote $quote)
    {
        $this->setQuote($quote);

        if ($quote->getPayment() && $quote->getPayment()->getMethod()) {
            $this->setMethodInstance($quote->getPayment()->getMethodInstance());
        }

        $orderInfo = $quote->getData();
        $this->_cleanupArray($orderInfo);
        $this->setOrderInfo($orderInfo);

        $addressInfo = $quote->getBillingAddress()->getData();
        $this->_cleanupArray($addressInfo);
        $this->setBillingAddressInfo($addressInfo);
        if (!$quote->isVirtual()) {
            $addressInfo = $quote->getShippingAddress()->getData();
            $this->_cleanupArray($addressInfo);
            $this->setShippingAddressInfo($addressInfo);
        }

        $this->setCurrencyCode($quote->getBaseCurrencyCode());
        $this->setCustomerId($quote->getCustomerId());
        $this->setStoreId($quote->getStoreId());

        return $this;
    }

    /**
     * Import quote item information to the profile
     *
     * @param \Magento\Sales\Model\Quote\Item\AbstractItem $item
     * @return \Magento\RecurringProfile\Model\Profile
     */
    public function importQuoteItem(\Magento\Sales\Model\Quote\Item\AbstractItem $item)
    {
        $this->setQuoteItemInfo($item);

        // TODO: make it abstract from amounts
        $this->setBillingAmount($item->getBaseRowTotal())
            ->setTaxAmount($item->getBaseTaxAmount())
            ->setShippingAmount($item->getBaseShippingAmount());
        if (!$this->getScheduleDescription()) {
            $this->setScheduleDescription($item->getName());
        }

        $orderItemInfo = $item->getData();
        $this->_cleanupArray($orderItemInfo);

        $customOptions = $item->getOptionsByCode();
        if ($customOptions['info_buyRequest']) {
            $orderItemInfo['info_buyRequest'] = $customOptions['info_buyRequest']->getValue();
        }

        $this->setOrderItemInfo($orderItemInfo);

        return $this->_filterValues();
    }

    /**
     * Render state as label
     *
     * @param string $key
     * @return mixed
     */
    public function renderData($key)
    {
        $value = $this->_getData($key);
        switch ($key) {
            case 'state':
                $states = $this->states->toOptionArray();
                return $states[$value];
        }
        return parent::renderData($key);
    }

    /**
     * Getter for additional information value
     * It is assumed that the specified additional info is an object or associative array
     *
     * @param string $infoKey
     * @param string $infoValueKey
     * @return mixed|null
     */
    public function getInfoValue($infoKey, $infoValueKey)
    {
        $info = $this->getData($infoKey);
        if (!$info) {
            return;
        }
        if (!is_object($info)) {
            if (is_array($info) && isset($info[$infoValueKey])) {
                return $info[$infoValueKey];
            }
        } else {
            if ($info instanceof \Magento\Object) {
                return $info->getDataUsingMethod($infoValueKey);
            } elseif (isset($info->$infoValueKey)) {
                return $info->$infoValueKey;
            }
        }
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\RecurringProfile\Model\Resource\Profile');
    }

    /**
     * Automatically set "unknown" state if not defined
     *
     * @return \Magento\RecurringProfile\Model\RecurringProfile
     */
    protected function _filterValues()
    {
        $result = parent::_filterValues();

        if (!$this->getState()) {
            $this->setState(States::STATE_UNKNOWN);
        }

        return $result;
    }

    /**
     * Initialize the workflow reference
     */
    protected function _initWorkflow()
    {
        if (null === $this->_workflow) {
            $this->_workflow = array(
                'unknown'   => array('pending', 'active', 'suspended', 'canceled'),
                'pending'   => array('active', 'canceled'),
                'active'    => array('suspended', 'canceled'),
                'suspended' => array('active', 'canceled'),
                'canceled'  => array(),
                'expired'   => array(),
            );
        }
    }

    /**
     * Check whether profile can be changed to specified state
     *
     * @param string $againstState
     * @param bool $soft
     * @return bool
     * @throws \Magento\Core\Exception
     */
    protected function _checkWorkflow($againstState, $soft = true)
    {
        $this->_initWorkflow();
        $state = $this->getState();
        $result = (!empty($this->_workflow[$state])) && in_array($againstState, $this->_workflow[$state]);
        if (!$soft && !$result) {
            throw new \Magento\Core\Exception(
                __('This profile state cannot be changed to "%1".', $againstState)
            );
        }
        return $result;
    }

    /**
     * Return recurring profile child orders Ids
     *
     * @return array
     */
    public function getChildOrderIds()
    {
        $ids = $this->_getResource()->getChildOrderIds($this);
        if (empty($ids)) {
            $ids[] = '-1';
        }
        return $ids;
    }

    /**
     * Add order relation to recurring profile
     *
     * @param int $orderId
     * @return \Magento\RecurringProfile\Model\Profile
     */
    public function addOrderRelation($orderId)
    {
        $this->getResource()->addOrderRelation($this->getId(), $orderId);
        return $this;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     *
     * @param \Magento\Object $itemInfo
     * @return \Magento\Sales\Model\Order\Item
     * @throws \Exception
     */
    protected function _getItem($itemInfo)
    {
        $paymentType = $itemInfo->getPaymentType();
        if (!$paymentType) {
            throw new \Exception("Recurring profile payment type is not specified.");
        }

        switch ($paymentType) {
            case self::PAYMENT_TYPE_REGULAR:
                return $this->_getRegularItem($itemInfo);
            case self::PAYMENT_TYPE_TRIAL:
                return $this->_getTrialItem($itemInfo);
            case self::PAYMENT_TYPE_INITIAL:
                return $this->_getInitialItem($itemInfo);
            default:
                new \Exception("Invalid recurring profile payment type '{$paymentType}'.");
        }
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for regular payment
     *
     * @param \Magento\Object $itemInfo
     * @return \Magento\Sales\Model\Order\Item
     */
    protected function _getRegularItem($itemInfo)
    {
        $price = $itemInfo->getPrice() ? $itemInfo->getPrice() : $this->getBillingAmount();
        $shippingAmount = $itemInfo->getShippingAmount() ? $itemInfo->getShippingAmount() : $this->getShippingAmount();
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : $this->getTaxAmount();

        $item = $this->_orderItemFactory->create()
            ->setData($this->getOrderItemInfo())
            ->setQtyOrdered($this->getInfoValue('order_item_info', 'qty'))
            ->setBaseOriginalPrice($this->getInfoValue('order_item_info', 'price'))
            ->setPrice($price)
            ->setBasePrice($price)
            ->setRowTotal($price)
            ->setBaseRowTotal($price)
            ->setTaxAmount($taxAmount)
            ->setShippingAmount($shippingAmount)
            ->setId(null);
        return $item;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for trial payment
     *
     * @param \Magento\Object $itemInfo
     * @return \Magento\Sales\Model\Order\Item
     */
    protected function _getTrialItem($itemInfo)
    {
        $item = $this->_getRegularItem($itemInfo);

        $item->setName(
            __('Trial ') . $item->getName()
        );

        $option = array(
            'label' => __('Payment type'),
            'value' => __('Trial period payment')
        );

        $this->_addAdditionalOptionToItem($item, $option);

        return $item;
    }

    /**
     * Create and return new order item based on profile item data and $itemInfo
     * for initial payment
     *
     * @param \Magento\Object $itemInfo
     * @return \Magento\Sales\Model\Order\Item
     */
    protected function _getInitialItem($itemInfo)
    {
        $price = $itemInfo->getPrice() ? $itemInfo->getPrice() : $this->getInitAmount();
        $shippingAmount = $itemInfo->getShippingAmount() ? $itemInfo->getShippingAmount() : 0;
        $taxAmount = $itemInfo->getTaxAmount() ? $itemInfo->getTaxAmount() : 0;
        $item = $this->_orderItemFactory->create()
            ->setStoreId($this->getStoreId())
            ->setProductType(\Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
            ->setIsVirtual(1)
            ->setSku('initial_fee')
            ->setName(__('Recurring Profile Initial Fee'))
            ->setDescription('')
            ->setWeight(0)
            ->setQtyOrdered(1)
            ->setPrice($price)
            ->setOriginalPrice($price)
            ->setBasePrice($price)
            ->setBaseOriginalPrice($price)
            ->setRowTotal($price)
            ->setBaseRowTotal($price)
            ->setTaxAmount($taxAmount)
            ->setShippingAmount($shippingAmount);

        $option = array(
            'label' => __('Payment type'),
            'value' => __('Initial period payment')
        );

        $this->_addAdditionalOptionToItem($item, $option);
        return $item;
    }

    /**
     * Add additional options suboption into item
     *
     * @param \Magento\Sales\Model\Order\Item $item
     * @param array $option
     */
    protected function _addAdditionalOptionToItem($item, $option)
    {
        $options = $item->getProductOptions();
        $additionalOptions = $item->getProductOptionByCode('additional_options');
        if (is_array($additionalOptions)) {
            $additionalOptions[] = $option;
        } else {
            $additionalOptions = array($option);
        }
        $options['additional_options'] = $additionalOptions;
        $item->setProductOptions($options);
    }

    /**
     * Recursively cleanup array from objects
     *
     * @param array &$array
     */
    private function _cleanupArray(&$array)
    {
        if (!$array) {
            return;
        }
        foreach ($array as $key => $value) {
            if (is_object($value)) {
                unset($array[$key]);
            } elseif (is_array($value)) {
                $this->_cleanupArray($array[$key]);
            }
        }
    }
}
