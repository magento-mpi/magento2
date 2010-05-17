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
    const STATE_PENDING   = 'pending';
    const STATE_ACTIVE    = 'active';
    const STATE_SUSPENDED = 'suspended';
    // cancelled

    /**
     * Submit a recurring profile right after an order is placed
     *
     *
     *
     * @param Mage_Sales_Model_Order_Item $orderItem
     */
    public function submit(Mage_Sales_Model_Order_Item $orderItem)
    {
        /* @var $order Mage_Sales_Model_Order */
        $order = $orderItem->getOrder();
        $methodInstance = $order->getPayment()->getMethodInstance();

        $this->setOrderItemId($orderItem->getId());
        $methodInstance->setStoreId($order->getStoreId())->submitRecurringPaymentProfile($this);
        $this->setInternalReferenceId("{$order->getIncrementId()}/{$orderItem->getId()}");

        if ($this->getReferenceId()) {
            $message = Mage::helper('sales')->__('Added recurring profile #%s for item "%s".', $this->getReferenceId(), $orderItem->getSku());
        } else {
            $message = Mage::helper('sales')->__('Added a mock recurring profile #%s for item "%s".', $this->getInternalReferenceId(), $orderItem->getSku());
        }
        $comment = $order->addStatusHistoryComment($message);

        try {
            Mage::getModel('core/resource_transaction')->addObject($this)->addObject($comment)->save();
        } catch (Mage_Core_Exception $e) {
            $order->addStatusHistoryComment(
                Mage::helper('sales')->__('Failed to create a recurring profile #%s for item "%s".', $this->getInternalReferenceId(), $orderItem->getSku())
            )->save();
            throw $e;
        }
    }

//    public function processSubmissionNotification()
//    {
//
//    }
//
//    public function suspend()
//    {
//
//    }
//
//    public function canSuspend()
//    {
//
//    }
//
//    public function activate()
//    {
//
//    }
//
//    public function canActivate()
//    {
//
//    }
//
//    public function billOutstandingAmount()
//    {
//
//    }
//
//    public function canBillOutstandingAmount()
//    {
//
//    }
//
//    public function processPaymentNotification()
//    {
//        // create new order basing on original order item and addresses
//    }
//
//    public function processStateNotification($state)
//    {
//
//    }

//    public function loadByInternalReferenceId($internalReferenceId)
//    {
//
//    }

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
     * Import quote item information to the profile
     *
     * @param Mage_Sales_Model_Quote_Item_Abstract $item
     * @return Mage_Sales_Model_Recurring_Profile
     */
    public function importQuoteItem(Mage_Sales_Model_Quote_Item_Abstract $item)
    {
        // TODO: make it abstract from amounts
        $this->setBillingAmount($item->getBaseRowTotal())
            ->setTaxAmount($item->getBaseTaxAmount())
            ->setShippingAmount($item->getBaseShippingAmount())
        ;
        if (!$this->getScheduleDescription()) {
            $this->setScheduleDescription($item->getname());
        }
        return $this->_filterValues();
    }

    /**
     * Automatically set "unknown" state if not defined
     *
     * @return Mage_Payment_Model_Recurring_Profile
     */
    protected function _filterValues()
    {
        $result = parent::_filterValues();

        // state
        if (!$this->getState()) {
            $this->setState(self::STATE_PENDING);
        }

        return $result;
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
                self::STATE_PENDING => Mage::helper('sales')->__('Not Initialized'),
            );
        }
        return array(self::STATE_PENDING);
    }

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('sales/recurring_profile');
    }

//    protected function _getMethodInstance()
//    {
//
//    }

    /**
     * Validate the state
     *
     * @throws Mage_Core_Exception
     */
    protected function _validateBeforeSave()
    {
        parent::_validateBeforeSave();
        if (!$this->getOrderItemId()) {
            Mage::throwException(Mage::helper('sales')->__('An order item ID is required to save a payment profile.'));
        }
    }
}
