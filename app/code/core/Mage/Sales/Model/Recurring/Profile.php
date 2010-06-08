<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Mage
 * @package     Mage_Sales
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * Sales implementation of recurring payment profiles
 * Implements saving and manageing profiles
 */
class Mage_Sales_Model_Recurring_Profile extends Mage_Payment_Model_Recurring_Profile
{
    /**
     * Available states
     *
     * @var string
     */
    const STATE_UNKNOWN   = 'unknown';
    const STATE_PENDING   = 'pending';
    const STATE_ACTIVE    = 'active';
    const STATE_SUSPENDED = 'suspended';
    const STATE_CANCELED  = 'canceled';

    /**
     * Allowed actions matrix
     *
     * @var array
     */
    protected $_workflow = null;

    /**
     * Load order by system increment identifier
     *
     * @param string $incrementId
     * @return Mage_Sales_Model_Order
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
            $this->setInternalReferenceId(Mage::helper('core')->uniqHash('temporary-'));
            $this->save();
            $this->setInternalReferenceId(Mage::helper('core')->uniqHash($this->getId() . '-'));
            $this->getMethodInstance()->submitRecurringProfile($this, $this->getQuote()->getPayment());
            $this->save();
            $this->_getResource()->commit();
        } catch (Exception $e) {
            $this->_getResource()->rollBack();
            throw $e;
        }
    }

//    public function activate()
//    {
//        $this->_checkWorkflow(self::STATE_ACTIVE, false);
//        $this->_initMethodInstance();
//        $this->setState(self::STATE_ACTIVE);
//        $this->_methodInstance->updateRecurringProfileStatus($this);
//
//        $refId = $this->getReferenceId();
//        $sku = $this->getOrderItem()->getSku();
//        $this->_updateOriginalOrder(
//            Mage::helper('sales')->__('Activated recurring profile #%s for item "%s".', $refId, $sku)
//        );
//    }
//
    public function canActivate()
    {
        return $this->_checkWorkflow(self::STATE_ACTIVE);
    }
//
//    public function suspend()
//    {
//        $this->_checkWorkflow(self::STATE_SUSPENDED, false);
//        $this->_initMethodInstance();
//    }
//
    public function canSuspend()
    {
        return $this->_checkWorkflow(self::STATE_SUSPENDED);
    }
//
//    public function cancel()
//    {
//        $this->_checkWorkflow(self::STATE_CANCELED, false);
//        $this->_initMethodInstance();
//    }
//
    public function canCancel()
    {
        return $this->_checkWorkflow(self::STATE_CANCELED);
    }
//
////    public function billOutstandingAmount($baseAmount, Mage_Payment_Model_Recurring_Profile_Info $info = null)
////    {
////
////    }
////
////    public function canBillOutstandingAmount()
////    {
////        // check method instance?
////    }
//
//    public function processSubmissionNotification(Mage_Payment_Model_Recurring_Profile_Info $info)
//    {
//        // confirmation that this profile was created on the gateway
//    }
//
//    public function processPaymentNotification(Mage_Payment_Model_Recurring_Profile_Info $info)
//    {
//        // create new order basing on original order item and addresses
//    }
//
//    public function processStateNotification(Mage_Payment_Model_Recurring_Profile_Info $info)
//    {
//        // update self state
//    }
//
////    public function loadByInternalReferenceId($internalReferenceId)
////    {
////
////    }

    /**
     * Register payment notification create and process order
     *
     */
    public function registerNewPayment($amount, $txn_id)
    {
        $order = $this->_createOrder();

        $amount = 15.96;
        $order    
            ->setBaseGrandTotal(15.96)
            ->setBaseSubtotal(10.01)
            ->setBaseShippingAmount(5.12)
            ->setBaseTaxAmount(0.83)
            
            ->setGrandTotal(15.96)
            ->setSubtotal(10.01)
            ->setShippingAmount(5.12)
            ->setTaxAmount(0.83);
                
                
                
            
        $payment = $order->getPayment();
        $payment->setTransactionId($txn_id)
            //->setPreparedMessage($this->_createIpnComment(''))
            //->setParentTransactionId($this->getRequestData('parent_txn_id'))
            //->setShouldCloseParentTransaction('Completed' === $this->getRequestData('auth_status'))
            //->setIsTransactionClosed(0)
            ->registerCaptureNotification($amount);
        
        $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($order)
                ->save();
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
        if (!in_array($this->getState(), $this->getAllStates(false), true)) {
            $this->_errors['state'][] = Mage::helper('sales')->__('Wrong state: "%s".', $this->getState());
        }

        return empty($this->_errors);
    }

    /**
     * Import quote information to the profile
     *
     * @param Mage_Sales_Model_Quote_ $quote
     * @return Mage_Sales_Model_Recurring_Profile
     */
    public function importQuote(Mage_Sales_Model_Quote $quote)
    {
        $this->setQuote($quote);

        if ($quote->getPayment() && $quote->getPayment()->getMethod()) {
            $this->setMethodInstance($quote->getPayment()->getMethodInstance());
        }

        $orderInfo = array();
        foreach ($quote->getData() as $kay => $value) {
            if (!is_object($value)) {
                $orderInfo[$kay] = $value;
            }
        }
        $this->setOrderInfo($orderInfo);

        $this->setBillingAddressInfo($quote->getBillingAddress()->getData());
        if (!$quote->isVirtual()) {
            $this->setShippingAddressInfo($quote->getShippingAddress()->getData());
        }

        $this->setCurrencyCode($quote->getBaseCurrencyCode());
        $this->setCustomerId($quote->getCustomerId());
        $this->setStoreId($quote->getStoreId());

        return $this;
    }

    /**
     * Import quote item information to the profile
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Mage_Sales_Model_Recurring_Profile
     */
    public function importQuoteItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        $this->setQuoteItemInfo($item);

        // TODO: make it abstract from amounts
        $this->setBillingAmount($item->getBaseRowTotal())
            ->setTaxAmount($item->getBaseTaxAmount())
            ->setShippingAmount($item->getBaseShippingAmount())
        ;
        if (!$this->getScheduleDescription()) {
            $this->setScheduleDescription($item->getName());
        }

        $orderItemInfo = array();
        foreach ($item->getData() as $kay => $value) {
            if (!is_object($value)) {
                $orderItemInfo[$kay] = $value;
            }
        }
        $this->setOrderItemInfo($orderItemInfo);

        return $this->_filterValues();
    }

    /**
     * Getter for sales-related field labels
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldLabel($field)
    {
        switch ($field) {
            case 'order_item_id':
                return Mage::helper('sales')->__('Purchased Item');
            case 'state':
                return Mage::helper('sales')->__('Profile State');
            case 'created_at':
                return Mage::helper('adminhtml')->__('Created At');
            case 'updated_at':
                return Mage::helper('adminhtml')->__('Updated At');
            default:
                return parent::getFieldLabel($field);
        }
    }

    /**
     * Getter for sales-related field comments
     *
     * @param string $field
     * @return string|null
     */
    public function getFieldComment($field)
    {
        switch ($field) {
            case 'order_item_id':
                return Mage::helper('sales')->__('Original order item that recurring payment profile correspondss to.');
            default:
                return parent::getFieldComment($field);
        }
    }

    /**
     * Getter for all available states
     *
     * @param bool $withLabels
     * @return array
     */
    public function getAllStates($withLabels = true)
    {
        $states = array(
            self::STATE_UNKNOWN, self::STATE_PENDING, self::STATE_ACTIVE, self::STATE_SUSPENDED, self::STATE_CANCELED
        );
        if ($withLabels) {
            $result = array();
            foreach ($states as $state) {
                $result[$state] = $this->getStateLabel($state);
            }
            return $result;
        }
        return $states;
    }

    /**
     * Get state label based on the code
     *
     * @param string $state
     * @return string
     */
    public function getStateLabel($state)
    {
        switch ($state) {
            case self::STATE_UNKNOWN:   return Mage::helper('sales')->__('Not Initialized');
            case self::STATE_PENDING:   return Mage::helper('sales')->__('Pending');
            case self::STATE_ACTIVE:    return Mage::helper('sales')->__('Active');
            case self::STATE_SUSPENDED: return Mage::helper('sales')->__('Suspended');
            case self::STATE_CANCELED:  return Mage::helper('sales')->__('Canceled');
            default: return $state;
        }
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
                return $this->getStateLabel($value);
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
            if ($info instanceof Varien_Object) {
                return $info->getDataUsingMethod($infoValueKey);
            } elseif (isset($info->$infoValueKey)) {
                return $info->$infoValueKey;
            }
        }
    }

    /**
     * Create order model with data from current profile
     * 
     * @return Sales_Model_Order
     */
    protected function _createOrder()
    {
        $order = Mage::getModel('sales/order');

        $transactionSave = Mage::getModel('core/resource_transaction')
            ->addObject($order)
            ->save();

        $quote = Mage::getModel('sales/quote')
            ->setStoreId($this->getStoreId())
            ->reserveOrderId();
        $orderIncrementId = $quote->getReservedOrderId(); 

        $orderItem = Mage::getModel('sales/order_item')
            ->setData($this->getOrderItemInfo())
            ->setId(null);

        $billingAddress = Mage::getModel('sales/order_address')
            ->setData($this->getBillingAddressInfo())
            ->setId(null);

        $shippingAddress = Mage::getModel('sales/order_address')
            ->setData($this->getShippingAddressInfo())
            ->setId(null);

        $payment = Mage::getModel('sales/order_payment')
            ->setMethod($this->getMethodCode());

        $transferDataKays = array(
            'store_id',
            'store_name',
            'customer_id',
            'customer_email',
            'customer_firstname',
            'customer_lastname',
            'customer_middlename',
            'customer_prefix',  
            'customer_suffix',
            'customer_taxvat',
            'customer_gender',
            'customer_is_guest',
            'customer_note_notify',
            'customer_group_id',
            'customer_note',
            'shipping_method',
            'shipping_description',
            'base_currency_code',
            'global_currency_code',
            'order_currency_code',
            'store_currency_code',
            'base_to_global_rate',
            'base_to_order_rate',
            'store_to_base_rate',
            'store_to_order_rate'
        );

        $orderInfo = $this->getOrderInfo();
        foreach ($transferDataKays as $kay) {
            if (isset($orderInfo[$kay])) {
                $order->setData($kay, $orderInfo[$kay]);
            }
        } 

        $order
            ->setTotalItemCount(1)    
            ->setIncrementId($orderIncrementId)
            ->setBillingAddress($billingAddress)
            ->setShippingAddress($shippingAddress)
            ->addItem($orderItem)
            ->setPayment($payment)
            ->setIsVirtual($orderItem->getIsVirtual())
            ->setWeight($orderItem->getWeight());

        $transactionSave = Mage::getModel('core/resource_transaction')
                ->addObject($order)
                ->save();

        return $order;
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/recurring_profile');
    }

    /**
     * Automatically set "unknown" state if not defined
     *
     * @return Mage_Payment_Model_Recurring_Profile
     */
    protected function _filterValues()
    {
        $result = parent::_filterValues();

        if (!$this->getState()) {
            $this->setState(self::STATE_UNKNOWN);
        }

        return $result;
    }

    /**
     * Initialize payment method instance from code
     *
     * @param int $storeId
     */
    protected function _initMethodInstance($storeId = null)
    {
        if (!$storeId && $this->getOriginalOrderItem()) {
            $storeId = $this->_originalOrderItem->getOrder()->getStoreId();
        }
        return parent::_initMethodInstance($storeId);
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
            );
        }
    }

    /**
     * Check whether profile can be changed to specified state
     *
     * @param string $againstState
     * @param bool $soft
     * @return bool
     * @throws Mage_Core_Exception
     */
    protected function _checkWorkflow($againstState, $soft = true)
    {
        $this->_initWorkflow();
        $state = $this->getState();
        $result = (!empty($this->_workflow[$state])) && in_array($againstState, $this->_workflow[$state]);
        if (!$soft && !$result) {
            Mage::throwException(
                Mage::helper('sales')->__('This profile state cannot be changed to "%s".', $againstState)
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
        if (empty($ids)){
            $ids[] = '-1';
        }
        return $ids;
    }

    /**
     * Return recurring profile child order items
     *
     * @return array
     */
    public function getItems()
    {
        return array();
    }
}
