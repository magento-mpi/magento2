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
//    public function canActivate()
//    {
//        $this->_checkWorkflow(self::STATE_ACTIVE);
//    }
//
//    public function suspend()
//    {
//        $this->_checkWorkflow(self::STATE_SUSPENDED, false);
//        $this->_initMethodInstance();
//    }
//
//    public function canSuspend()
//    {
//        $this->_checkWorkflow(self::STATE_SUSPENDED);
//    }
//
//    public function cancel()
//    {
//        $this->_checkWorkflow(self::STATE_CANCELED, false);
//        $this->_initMethodInstance();
//    }
//
//    public function canCancel()
//    {
//        $this->_checkWorkflow(self::STATE_CANCELED);
//    }
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
        if ($withLabels) {
            return array(
                self::STATE_UNKNOWN   => Mage::helper('sales')->__('Not Initialized'),
                self::STATE_PENDING   => Mage::helper('sales')->__('Pending'),
                self::STATE_ACTIVE    => Mage::helper('sales')->__('Active'),
                self::STATE_SUSPENDED => Mage::helper('sales')->__('Suspended'),
                self::STATE_CANCELED  => Mage::helper('sales')->__('Canceled'),
            );
        }
        return array(
            self::STATE_UNKNOWN, self::STATE_PENDING, self::STATE_ACTIVE, self::STATE_SUSPENDED, self::STATE_CANCELED
        );
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
        if (!$this->getCustomerId()) {
            $this->setCustomerId(null);
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
