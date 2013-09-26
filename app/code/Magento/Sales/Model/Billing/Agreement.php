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
 * Billing Agreement abstract model
 *
 * @method Magento_Sales_Model_Resource_Billing_Agreement _getResource()
 * @method Magento_Sales_Model_Resource_Billing_Agreement getResource()
 * @method int getCustomerId()
 * @method Magento_Sales_Model_Billing_Agreement setCustomerId(int $value)
 * @method string getMethodCode()
 * @method Magento_Sales_Model_Billing_Agreement setMethodCode(string $value)
 * @method string getReferenceId()
 * @method Magento_Sales_Model_Billing_Agreement setReferenceId(string $value)
 * @method string getStatus()
 * @method Magento_Sales_Model_Billing_Agreement setStatus(string $value)
 * @method string getCreatedAt()
 * @method Magento_Sales_Model_Billing_Agreement setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method Magento_Sales_Model_Billing_Agreement setUpdatedAt(string $value)
 * @method int getStoreId()
 * @method Magento_Sales_Model_Billing_Agreement setStoreId(int $value)
 * @method string getAgreementLabel()
 * @method Magento_Sales_Model_Billing_Agreement setAgreementLabel(string $value)
 */
class Magento_Sales_Model_Billing_Agreement extends Magento_Payment_Model_Billing_AgreementAbstract
{
    const STATUS_ACTIVE     = 'active';
    const STATUS_CANCELED   = 'canceled';

    /**
     * Related agreement orders
     *
     * @var array
     */
    protected $_relatedOrders = array();

    /**
     * @var Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory
     */
    protected $_billingAgreementFactory;

    /**
     * @var Magento_Core_Model_Date
     */
    protected $_dateFactory;

    /**
     * @param Magento_Payment_Helper_Data $paymentData
     * @param Magento_Core_Model_Context $context
     * @param Magento_Core_Model_Registry $registry
     * @param Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory $billingAgreementFactory
     * @param Magento_Core_Model_Date $dateFactory
     * @param Magento_Core_Model_Resource_Abstract $resource
     * @param Magento_Data_Collection_Db $resourceCollection
     * @param array $data
     */
    public function __construct(
        Magento_Payment_Helper_Data $paymentData,
        Magento_Core_Model_Context $context,
        Magento_Core_Model_Registry $registry,
        Magento_Sales_Model_Resource_Billing_Agreement_CollectionFactory $billingAgreementFactory,
        Magento_Core_Model_Date $dateFactory,
        Magento_Core_Model_Resource_Abstract $resource = null,
        Magento_Data_Collection_Db $resourceCollection = null,
        array $data = array()
    ) {
        parent::__construct($paymentData, $context, $registry, $resource, $resourceCollection, $data);
        $this->_billingAgreementFactory = $billingAgreementFactory;
        $this->_dateFactory = $dateFactory;
    }

    /**
     * Init model
     */
    protected function _construct()
    {
        $this->_init('Magento_Sales_Model_Resource_Billing_Agreement');
    }

    /**
     * Set created_at parameter
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _beforeSave()
    {
        $date = $this->_dateFactory->create()->gmtDate();
        if ($this->isObjectNew() && !$this->getCreatedAt()) {
            $this->setCreatedAt($date);
        } else {
            $this->setUpdatedAt($date);
        }
        return parent::_beforeSave();
    }

    /**
     * Save agreement order relations
     *
     * @return Magento_Core_Model_Abstract
     */
    protected function _afterSave()
    {
        if (!empty($this->_relatedOrders)) {
            $this->_saveOrderRelations();
        }
        return parent::_afterSave();
    }

    /**
     * Retrieve billing agreement status label
     *
     * @return string
     */
    public function getStatusLabel()
    {
        switch ($this->getStatus()) {
            case self::STATUS_ACTIVE:
                return __('Active');
            case self::STATUS_CANCELED:
                return __('Canceled');
            default:
                return '';
        }
    }

    /**
     * Initialize token
     *
     * @return string
     */
    public function initToken()
    {
        $this->getPaymentMethodInstance()
            ->initBillingAgreementToken($this);
        return $this->getRedirectUrl();
    }

    /**
     * Get billing agreement details
     * Data from response is inside this object
     *
     * @return Magento_Sales_Model_Billing_Agreement
     */
    public function verifyToken()
    {
        $this->getPaymentMethodInstance()
            ->getBillingAgreementTokenInfo($this);
        return $this;
    }

    /**
     * Create billing agreement
     *
     * @return Magento_Sales_Model_Billing_Agreement
     */
    public function place()
    {
        $this->verifyToken();

        $paymentMethodInstance = $this->getPaymentMethodInstance()
            ->placeBillingAgreement($this);

        $this->setCustomerId($this->getCustomer()->getId())
            ->setMethodCode($this->getMethodCode())
            ->setReferenceId($this->getBillingAgreementId())
            ->setStatus(self::STATUS_ACTIVE)
            ->setAgreementLabel($paymentMethodInstance->getTitle())
            ->save();
        return $this;
    }

    /**
     * Cancel billing agreement
     *
     * @return Magento_Sales_Model_Billing_Agreement
     */
    public function cancel()
    {
        $this->setStatus(self::STATUS_CANCELED);
        $this->getPaymentMethodInstance()->updateBillingAgreementStatus($this);
        return $this->save();
    }

    /**
     * Check whether can cancel billing agreement
     *
     * @return bool
     */
    public function canCancel()
    {
        return ($this->getStatus() != self::STATUS_CANCELED);
    }

    /**
     * Retrieve billing agreement statuses array
     *
     * @return array
     */
    public function getStatusesArray()
    {
        return array(
            self::STATUS_ACTIVE     => __('Active'),
            self::STATUS_CANCELED   => __('Canceled')
        );
    }

    /**
     * Validate data
     *
     * @return bool
     */
    public function isValid()
    {
        $result = parent::isValid();
        if (!$this->getCustomerId()) {
            $this->_errors[] = __('The customer ID is not set.');
        }
        if (!$this->getStatus()) {
            $this->_errors[] = __('The Billing Agreement status is not set.');
        }
        return $result && empty($this->_errors);
    }

    /**
     * Import payment data to billing agreement
     *
     * $payment->getBillingAgreementData() contains array with following structure :
     *  [billing_agreement_id]  => string
     *  [method_code]           => string
     *
     * @param Magento_Sales_Model_Order_Payment $payment
     * @return Magento_Sales_Model_Billing_Agreement
     */
    public function importOrderPayment(Magento_Sales_Model_Order_Payment $payment)
    {
        $baData = $payment->getBillingAgreementData();

        $this->_paymentMethodInstance = (isset($baData['method_code']))
            ? $this->_paymentData->getMethodInstance($baData['method_code'])
            : $payment->getMethodInstance();
        if ($this->_paymentMethodInstance) {
            $this->_paymentMethodInstance->setStore($payment->getMethodInstance()->getStore());
            $this->setCustomerId($payment->getOrder()->getCustomerId())
                ->setMethodCode($this->_paymentMethodInstance->getCode())
                ->setReferenceId($baData['billing_agreement_id'])
                ->setStatus(self::STATUS_ACTIVE);
        }
        return $this;
    }

    /**
     * Retrieve available customer Billing Agreements
     *
     * @param int $customerId
     * @return Magento_Sales_Model_Resource_Billing_Agreement_Collection
     */
    public function getAvailableCustomerBillingAgreements($customerId)
    {
        $collection = $this->_billingAgreementFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId)
            ->addFieldToFilter('status', self::STATUS_ACTIVE)
            ->setOrder('agreement_id');
        return $collection;
    }

    /**
     * Check whether need to create billing agreement for customer
     *
     * @param int $customerId
     * @return bool
     */
    public function needToCreateForCustomer($customerId)
    {
        return $customerId ? count($this->getAvailableCustomerBillingAgreements($customerId)) == 0 : false;
    }

    /**
     * Add order relation to current billing agreement
     *
     * @param int|Magento_Sales_Model_Order $orderId
     * @return Magento_Sales_Model_Billing_Agreement
     */
    public function addOrderRelation($orderId)
    {
        $this->_relatedOrders[] = $orderId;
        return $this;
    }

    /**
     * Save related orders
     */
    protected function _saveOrderRelations()
    {
        foreach ($this->_relatedOrders as $order) {
            $orderId = $order instanceof Magento_Sales_Model_Order ? $order->getId() : (int)$order;
            $this->getResource()->addOrderRelation($this->getId(), $orderId);
        }
    }
}
