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
 * Order model
 *
 * Supported events:
 *  sales_order_load_after
 *  sales_order_save_before
 *  sales_order_save_after
 *  sales_order_delete_before
 *  sales_order_delete_after
 *
 * @method \Magento\Sales\Model\Resource\Order _getResource()
 * @method \Magento\Sales\Model\Resource\Order getResource()
 * @method string getState()
 * @method string getStatus()
 * @method \Magento\Sales\Model\Order setStatus(string $value)
 * @method string getCouponCode()
 * @method \Magento\Sales\Model\Order setCouponCode(string $value)
 * @method string getProtectCode()
 * @method \Magento\Sales\Model\Order setProtectCode(string $value)
 * @method string getShippingDescription()
 * @method \Magento\Sales\Model\Order setShippingDescription(string $value)
 * @method int getIsVirtual()
 * @method \Magento\Sales\Model\Order setIsVirtual(int $value)
 * @method int getStoreId()
 * @method \Magento\Sales\Model\Order setStoreId(int $value)
 * @method int getCustomerId()
 * @method \Magento\Sales\Model\Order setCustomerId(int $value)
 * @method float getBaseDiscountAmount()
 * @method \Magento\Sales\Model\Order setBaseDiscountAmount(float $value)
 * @method float getBaseDiscountCanceled()
 * @method \Magento\Sales\Model\Order setBaseDiscountCanceled(float $value)
 * @method float getBaseDiscountInvoiced()
 * @method \Magento\Sales\Model\Order setBaseDiscountInvoiced(float $value)
 * @method float getBaseDiscountRefunded()
 * @method \Magento\Sales\Model\Order setBaseDiscountRefunded(float $value)
 * @method float getBaseGrandTotal()
 * @method \Magento\Sales\Model\Order setBaseGrandTotal(float $value)
 * @method float getBaseShippingAmount()
 * @method \Magento\Sales\Model\Order setBaseShippingAmount(float $value)
 * @method float getBaseShippingCanceled()
 * @method \Magento\Sales\Model\Order setBaseShippingCanceled(float $value)
 * @method float getBaseShippingInvoiced()
 * @method \Magento\Sales\Model\Order setBaseShippingInvoiced(float $value)
 * @method float getBaseShippingRefunded()
 * @method \Magento\Sales\Model\Order setBaseShippingRefunded(float $value)
 * @method float getBaseShippingTaxAmount()
 * @method \Magento\Sales\Model\Order setBaseShippingTaxAmount(float $value)
 * @method float getBaseShippingTaxRefunded()
 * @method \Magento\Sales\Model\Order setBaseShippingTaxRefunded(float $value)
 * @method float getBaseSubtotal()
 * @method \Magento\Sales\Model\Order setBaseSubtotal(float $value)
 * @method float getBaseSubtotalCanceled()
 * @method \Magento\Sales\Model\Order setBaseSubtotalCanceled(float $value)
 * @method float getBaseSubtotalInvoiced()
 * @method \Magento\Sales\Model\Order setBaseSubtotalInvoiced(float $value)
 * @method float getBaseSubtotalRefunded()
 * @method \Magento\Sales\Model\Order setBaseSubtotalRefunded(float $value)
 * @method float getBaseTaxAmount()
 * @method \Magento\Sales\Model\Order setBaseTaxAmount(float $value)
 * @method float getBaseTaxCanceled()
 * @method \Magento\Sales\Model\Order setBaseTaxCanceled(float $value)
 * @method float getBaseTaxInvoiced()
 * @method \Magento\Sales\Model\Order setBaseTaxInvoiced(float $value)
 * @method float getBaseTaxRefunded()
 * @method \Magento\Sales\Model\Order setBaseTaxRefunded(float $value)
 * @method float getBaseToGlobalRate()
 * @method \Magento\Sales\Model\Order setBaseToGlobalRate(float $value)
 * @method float getBaseToOrderRate()
 * @method \Magento\Sales\Model\Order setBaseToOrderRate(float $value)
 * @method float getBaseTotalCanceled()
 * @method \Magento\Sales\Model\Order setBaseTotalCanceled(float $value)
 * @method float getBaseTotalInvoiced()
 * @method \Magento\Sales\Model\Order setBaseTotalInvoiced(float $value)
 * @method float getBaseTotalInvoicedCost()
 * @method \Magento\Sales\Model\Order setBaseTotalInvoicedCost(float $value)
 * @method float getBaseTotalOfflineRefunded()
 * @method \Magento\Sales\Model\Order setBaseTotalOfflineRefunded(float $value)
 * @method float getBaseTotalOnlineRefunded()
 * @method \Magento\Sales\Model\Order setBaseTotalOnlineRefunded(float $value)
 * @method float getBaseTotalPaid()
 * @method \Magento\Sales\Model\Order setBaseTotalPaid(float $value)
 * @method float getBaseTotalQtyOrdered()
 * @method \Magento\Sales\Model\Order setBaseTotalQtyOrdered(float $value)
 * @method float getBaseTotalRefunded()
 * @method \Magento\Sales\Model\Order setBaseTotalRefunded(float $value)
 * @method float getDiscountAmount()
 * @method \Magento\Sales\Model\Order setDiscountAmount(float $value)
 * @method float getDiscountCanceled()
 * @method \Magento\Sales\Model\Order setDiscountCanceled(float $value)
 * @method float getDiscountInvoiced()
 * @method \Magento\Sales\Model\Order setDiscountInvoiced(float $value)
 * @method float getDiscountRefunded()
 * @method \Magento\Sales\Model\Order setDiscountRefunded(float $value)
 * @method float getGrandTotal()
 * @method \Magento\Sales\Model\Order setGrandTotal(float $value)
 * @method float getShippingAmount()
 * @method \Magento\Sales\Model\Order setShippingAmount(float $value)
 * @method float getShippingCanceled()
 * @method \Magento\Sales\Model\Order setShippingCanceled(float $value)
 * @method float getShippingInvoiced()
 * @method \Magento\Sales\Model\Order setShippingInvoiced(float $value)
 * @method float getShippingRefunded()
 * @method \Magento\Sales\Model\Order setShippingRefunded(float $value)
 * @method float getShippingTaxAmount()
 * @method \Magento\Sales\Model\Order setShippingTaxAmount(float $value)
 * @method float getShippingTaxRefunded()
 * @method \Magento\Sales\Model\Order setShippingTaxRefunded(float $value)
 * @method float getStoreToBaseRate()
 * @method \Magento\Sales\Model\Order setStoreToBaseRate(float $value)
 * @method float getStoreToOrderRate()
 * @method \Magento\Sales\Model\Order setStoreToOrderRate(float $value)
 * @method float getSubtotal()
 * @method \Magento\Sales\Model\Order setSubtotal(float $value)
 * @method float getSubtotalCanceled()
 * @method \Magento\Sales\Model\Order setSubtotalCanceled(float $value)
 * @method float getSubtotalInvoiced()
 * @method \Magento\Sales\Model\Order setSubtotalInvoiced(float $value)
 * @method float getSubtotalRefunded()
 * @method \Magento\Sales\Model\Order setSubtotalRefunded(float $value)
 * @method float getTaxAmount()
 * @method \Magento\Sales\Model\Order setTaxAmount(float $value)
 * @method float getTaxCanceled()
 * @method \Magento\Sales\Model\Order setTaxCanceled(float $value)
 * @method float getTaxInvoiced()
 * @method \Magento\Sales\Model\Order setTaxInvoiced(float $value)
 * @method float getTaxRefunded()
 * @method \Magento\Sales\Model\Order setTaxRefunded(float $value)
 * @method float getTotalCanceled()
 * @method \Magento\Sales\Model\Order setTotalCanceled(float $value)
 * @method float getTotalInvoiced()
 * @method \Magento\Sales\Model\Order setTotalInvoiced(float $value)
 * @method float getTotalOfflineRefunded()
 * @method \Magento\Sales\Model\Order setTotalOfflineRefunded(float $value)
 * @method float getTotalOnlineRefunded()
 * @method \Magento\Sales\Model\Order setTotalOnlineRefunded(float $value)
 * @method float getTotalPaid()
 * @method \Magento\Sales\Model\Order setTotalPaid(float $value)
 * @method float getTotalQtyOrdered()
 * @method \Magento\Sales\Model\Order setTotalQtyOrdered(float $value)
 * @method float getTotalRefunded()
 * @method \Magento\Sales\Model\Order setTotalRefunded(float $value)
 * @method int getCanShipPartially()
 * @method \Magento\Sales\Model\Order setCanShipPartially(int $value)
 * @method int getCanShipPartiallyItem()
 * @method \Magento\Sales\Model\Order setCanShipPartiallyItem(int $value)
 * @method int getCustomerIsGuest()
 * @method \Magento\Sales\Model\Order setCustomerIsGuest(int $value)
 * @method int getCustomerNoteNotify()
 * @method \Magento\Sales\Model\Order setCustomerNoteNotify(int $value)
 * @method int getBillingAddressId()
 * @method \Magento\Sales\Model\Order setBillingAddressId(int $value)
 * @method int getCustomerGroupId()
 * @method \Magento\Sales\Model\Order setCustomerGroupId(int $value)
 * @method int getEditIncrement()
 * @method \Magento\Sales\Model\Order setEditIncrement(int $value)
 * @method int getEmailSent()
 * @method \Magento\Sales\Model\Order setEmailSent(int $value)
 * @method int getForcedShipmentWithInvoice()
 * @method \Magento\Sales\Model\Order setForcedShipmentWithInvoice(int $value)
 * @method int getGiftMessageId()
 * @method \Magento\Sales\Model\Order setGiftMessageId(int $value)
 * @method int getPaymentAuthExpiration()
 * @method \Magento\Sales\Model\Order setPaymentAuthExpiration(int $value)
 * @method int getPaypalIpnCustomerNotified()
 * @method \Magento\Sales\Model\Order setPaypalIpnCustomerNotified(int $value)
 * @method int getQuoteAddressId()
 * @method \Magento\Sales\Model\Order setQuoteAddressId(int $value)
 * @method int getQuoteId()
 * @method \Magento\Sales\Model\Order setQuoteId(int $value)
 * @method int getShippingAddressId()
 * @method \Magento\Sales\Model\Order setShippingAddressId(int $value)
 * @method float getAdjustmentNegative()
 * @method \Magento\Sales\Model\Order setAdjustmentNegative(float $value)
 * @method float getAdjustmentPositive()
 * @method \Magento\Sales\Model\Order setAdjustmentPositive(float $value)
 * @method float getBaseAdjustmentNegative()
 * @method \Magento\Sales\Model\Order setBaseAdjustmentNegative(float $value)
 * @method float getBaseAdjustmentPositive()
 * @method \Magento\Sales\Model\Order setBaseAdjustmentPositive(float $value)
 * @method float getBaseShippingDiscountAmount()
 * @method \Magento\Sales\Model\Order setBaseShippingDiscountAmount(float $value)
 * @method float getBaseSubtotalInclTax()
 * @method \Magento\Sales\Model\Order setBaseSubtotalInclTax(float $value)
 * @method \Magento\Sales\Model\Order setBaseTotalDue(float $value)
 * @method float getPaymentAuthorizationAmount()
 * @method \Magento\Sales\Model\Order setPaymentAuthorizationAmount(float $value)
 * @method float getShippingDiscountAmount()
 * @method \Magento\Sales\Model\Order setShippingDiscountAmount(float $value)
 * @method float getSubtotalInclTax()
 * @method \Magento\Sales\Model\Order setSubtotalInclTax(float $value)
 * @method \Magento\Sales\Model\Order setTotalDue(float $value)
 * @method float getWeight()
 * @method \Magento\Sales\Model\Order setWeight(float $value)
 * @method string getCustomerDob()
 * @method \Magento\Sales\Model\Order setCustomerDob(string $value)
 * @method string getIncrementId()
 * @method \Magento\Sales\Model\Order setIncrementId(string $value)
 * @method string getAppliedRuleIds()
 * @method \Magento\Sales\Model\Order setAppliedRuleIds(string $value)
 * @method string getBaseCurrencyCode()
 * @method \Magento\Sales\Model\Order setBaseCurrencyCode(string $value)
 * @method string getCustomerEmail()
 * @method \Magento\Sales\Model\Order setCustomerEmail(string $value)
 * @method string getCustomerFirstname()
 * @method \Magento\Sales\Model\Order setCustomerFirstname(string $value)
 * @method string getCustomerLastname()
 * @method \Magento\Sales\Model\Order setCustomerLastname(string $value)
 * @method string getCustomerMiddlename()
 * @method \Magento\Sales\Model\Order setCustomerMiddlename(string $value)
 * @method string getCustomerPrefix()
 * @method \Magento\Sales\Model\Order setCustomerPrefix(string $value)
 * @method string getCustomerSuffix()
 * @method \Magento\Sales\Model\Order setCustomerSuffix(string $value)
 * @method string getCustomerTaxvat()
 * @method \Magento\Sales\Model\Order setCustomerTaxvat(string $value)
 * @method string getDiscountDescription()
 * @method \Magento\Sales\Model\Order setDiscountDescription(string $value)
 * @method string getExtCustomerId()
 * @method \Magento\Sales\Model\Order setExtCustomerId(string $value)
 * @method string getExtOrderId()
 * @method \Magento\Sales\Model\Order setExtOrderId(string $value)
 * @method string getGlobalCurrencyCode()
 * @method \Magento\Sales\Model\Order setGlobalCurrencyCode(string $value)
 * @method string getHoldBeforeState()
 * @method \Magento\Sales\Model\Order setHoldBeforeState(string $value)
 * @method string getHoldBeforeStatus()
 * @method \Magento\Sales\Model\Order setHoldBeforeStatus(string $value)
 * @method string getOrderCurrencyCode()
 * @method \Magento\Sales\Model\Order setOrderCurrencyCode(string $value)
 * @method string getOriginalIncrementId()
 * @method \Magento\Sales\Model\Order setOriginalIncrementId(string $value)
 * @method string getRelationChildId()
 * @method \Magento\Sales\Model\Order setRelationChildId(string $value)
 * @method string getRelationChildRealId()
 * @method \Magento\Sales\Model\Order setRelationChildRealId(string $value)
 * @method string getRelationParentId()
 * @method \Magento\Sales\Model\Order setRelationParentId(string $value)
 * @method string getRelationParentRealId()
 * @method \Magento\Sales\Model\Order setRelationParentRealId(string $value)
 * @method string getRemoteIp()
 * @method \Magento\Sales\Model\Order setRemoteIp(string $value)
 * @method string getShippingMethod()
 * @method \Magento\Sales\Model\Order setShippingMethod(string $value)
 * @method string getStoreCurrencyCode()
 * @method \Magento\Sales\Model\Order setStoreCurrencyCode(string $value)
 * @method string getStoreName()
 * @method \Magento\Sales\Model\Order setStoreName(string $value)
 * @method string getXForwardedFor()
 * @method \Magento\Sales\Model\Order setXForwardedFor(string $value)
 * @method string getCustomerNote()
 * @method \Magento\Sales\Model\Order setCustomerNote(string $value)
 * @method string getCreatedAt()
 * @method \Magento\Sales\Model\Order setCreatedAt(string $value)
 * @method string getUpdatedAt()
 * @method \Magento\Sales\Model\Order setUpdatedAt(string $value)
 * @method int getTotalItemCount()
 * @method \Magento\Sales\Model\Order setTotalItemCount(int $value)
 * @method int getCustomerGender()
 * @method \Magento\Sales\Model\Order setCustomerGender(int $value)
 * @method float getHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order setHiddenTaxAmount(float $value)
 * @method float getBaseHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order setBaseHiddenTaxAmount(float $value)
 * @method float getShippingHiddenTaxAmount()
 * @method \Magento\Sales\Model\Order setShippingHiddenTaxAmount(float $value)
 * @method float getBaseShippingHiddenTaxAmnt()
 * @method \Magento\Sales\Model\Order setBaseShippingHiddenTaxAmnt(float $value)
 * @method float getHiddenTaxInvoiced()
 * @method \Magento\Sales\Model\Order setHiddenTaxInvoiced(float $value)
 * @method float getBaseHiddenTaxInvoiced()
 * @method \Magento\Sales\Model\Order setBaseHiddenTaxInvoiced(float $value)
 * @method float getHiddenTaxRefunded()
 * @method \Magento\Sales\Model\Order setHiddenTaxRefunded(float $value)
 * @method float getBaseHiddenTaxRefunded()
 * @method \Magento\Sales\Model\Order setBaseHiddenTaxRefunded(float $value)
 * @method float getShippingInclTax()
 * @method \Magento\Sales\Model\Order setShippingInclTax(float $value)
 * @method float getBaseShippingInclTax()
 * @method \Magento\Sales\Model\Order setBaseShippingInclTax(float $value)
 *
 * @category    Magento
 * @package     Magento_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Sales\Model;

class Order extends \Magento\Sales\Model\AbstractModel
{
    const ENTITY                                = 'order';
    /**
     * XML configuration paths
     */
    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/order/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/order/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/order/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/order/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/order/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/order/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/order_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/order_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/order_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/order_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/order_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/order_comment/enabled';

    /**
     * Order states
     */
    const STATE_NEW             = 'new';
    const STATE_PENDING_PAYMENT = 'pending_payment';
    const STATE_PROCESSING      = 'processing';
    const STATE_COMPLETE        = 'complete';
    const STATE_CLOSED          = 'closed';
    const STATE_CANCELED        = 'canceled';
    const STATE_HOLDED          = 'holded';
    const STATE_PAYMENT_REVIEW  = 'payment_review';

    /**
     * Order statuses
     */
    const STATUS_FRAUD  = 'fraud';

    /**
     * Order flags
     */
    const ACTION_FLAG_CANCEL    = 'cancel';
    const ACTION_FLAG_HOLD      = 'hold';
    const ACTION_FLAG_UNHOLD    = 'unhold';
    const ACTION_FLAG_EDIT      = 'edit';
    const ACTION_FLAG_CREDITMEMO= 'creditmemo';
    const ACTION_FLAG_INVOICE   = 'invoice';
    const ACTION_FLAG_REORDER   = 'reorder';
    const ACTION_FLAG_SHIP      = 'ship';
    const ACTION_FLAG_COMMENT   = 'comment';

    /**
     * Report date types
     */
    const REPORT_DATE_TYPE_CREATED = 'created';
    const REPORT_DATE_TYPE_UPDATED = 'updated';
    /*
     * Identifier for history item
     */
    const HISTORY_ENTITY_NAME = 'order';

    protected $_eventPrefix = 'sales_order';
    protected $_eventObject = 'order';

    protected $_addresses       = null;
    protected $_items           = null;
    protected $_payments        = null;
    protected $_statusHistory   = null;
    protected $_invoices;
    protected $_tracks;
    protected $_shipments;
    protected $_creditmemos;

    protected $_relatedObjects  = array();
    protected $_orderCurrency   = null;
    protected $_baseCurrency    = null;

    /**
     * Array of action flags for canUnhold, canEdit, etc.
     *
     * @var array
     */
    protected $_actionFlag = array();

    /**
     * Flag: if after order placing we can send new email to the customer.
     *
     * @var bool
     */
    protected $_canSendNewEmailFlag = true;

    /*
     * Identifier for history item
     *
     * @var string
     */
    protected $_historyEntityName = self::HISTORY_ENTITY_NAME;

    /**
     * Initialize resource model
     */
    protected function _construct()
    {
        $this->_init('Magento\Sales\Model\Resource\Order');
    }

    /**
     * Clear order object data
     *
     * @param string $key data key
     * @return \Magento\Sales\Model\Order
     */
    public function unsetData($key=null)
    {
        parent::unsetData($key);
        if (is_null($key)) {
            $this->_items = null;
        }
        return $this;
    }

    /**
     * Retrieve can flag for action (edit, unhold, etc..)
     *
     * @param string $action
     * @return boolean|null
     */
    public function getActionFlag($action)
    {
        if (isset($this->_actionFlag[$action])) {
            return $this->_actionFlag[$action];
        }
        return null;
    }

    /**
     * Set can flag value for action (edit, unhold, etc...)
     *
     * @param string $action
     * @param boolean $flag
     * @return \Magento\Sales\Model\Order
     */
    public function setActionFlag($action, $flag)
    {
        $this->_actionFlag[$action] = (boolean) $flag;
        return $this;
    }

    /**
     * Return flag for order if it can sends new email to customer.
     *
     * @return bool
     */
    public function getCanSendNewEmailFlag()
    {
        return $this->_canSendNewEmailFlag;
    }

    /**
     * Set flag for order if it can sends new email to customer.
     *
     * @param bool $flag
     * @return \Magento\Sales\Model\Order
     */
    public function setCanSendNewEmailFlag($flag)
    {
        $this->_canSendNewEmailFlag = (boolean) $flag;
        return $this;
    }

    /**
     * Load order by system increment identifier
     *
     * @param string $incrementId
     * @return \Magento\Sales\Model\Order
     */
    public function loadByIncrementId($incrementId)
    {
        return $this->loadByAttribute('increment_id', $incrementId);
    }

    /**
     * Load order by custom attribute value. Attribute value should be unique
     *
     * @param string $attribute
     * @param string $value
     * @return \Magento\Sales\Model\Order
     */
    public function loadByAttribute($attribute, $value)
    {
        $this->load($value, $attribute);
        return $this;
    }

    /**
     * Retrieve store model instance
     *
     * @return \Magento\Core\Model\Store
     */
    public function getStore()
    {
        $storeId = $this->getStoreId();
        if ($storeId) {
            return \Mage::app()->getStore($storeId);
        }
        return \Mage::app()->getStore();
    }

    /**
     * Retrieve order cancel availability
     *
     * @return bool
     */
    public function canCancel()
    {
        if ($this->canUnhold()) {  // $this->isPaymentReview()
            return false;
        }

        if (!$this->canReviewPayment() && $this->canFetchPaymentReviewUpdate()) {
            return false;
        }

        $allInvoiced = true;
        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()) {
                $allInvoiced = false;
                break;
            }
        }
        if ($allInvoiced) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_CANCEL) === false) {
            return false;
        }

        /**
         * Use only state for availability detect
         */
        /*foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToCancel()>0) {
                return true;
            }
        }
        return false;*/
        return true;
    }

    /**
     * Getter whether the payment can be voided
     * @return bool
     */
    public function canVoidPayment()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }
        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }
        return $this->getPayment()->canVoid(new \Magento\Object);
    }

    /**
     * Retrieve order invoice availability
     *
     * @return bool
     */
    public function canInvoice()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }
        $state = $this->getState();
        if ($this->isCanceled() || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_INVOICE) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToInvoice()>0 && !$item->getLockedDoInvoice()) {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order credit memo (refund) availability
     *
     * @return bool
     */
    public function canCreditmemo()
    {
        if ($this->hasForcedCanCreditmemo()) {
            return $this->getForcedCanCreditmemo();
        }

        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->isCanceled() || $this->getState() === self::STATE_CLOSED) {
            return false;
        }

        /**
         * We can have problem with float in php (on some server $a=762.73;$b=762.73; $a-$b!=0)
         * for this we have additional diapason for 0
         * TotalPaid - contains amount, that were not rounded.
         */
        if (abs($this->getStore()->roundPrice($this->getTotalPaid()) - $this->getTotalRefunded()) < .0001) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order hold availability
     *
     * @return bool
     */
    public function canHold()
    {
        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED || $state === self::STATE_HOLDED) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_HOLD) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order unhold availability
     *
     * @return bool
     */
    public function canUnhold()
    {
        if ($this->getActionFlag(self::ACTION_FLAG_UNHOLD) === false || $this->isPaymentReview()) {
            return false;
        }
        return $this->getState() === self::STATE_HOLDED;
    }

    /**
     * Check if comment can be added to order history
     *
     * @return bool
     */
    public function canComment()
    {
        if ($this->getActionFlag(self::ACTION_FLAG_COMMENT) === false) {
            return false;
        }
        return true;
    }

    /**
     * Retrieve order shipment availability
     *
     * @return bool
     */
    public function canShip()
    {
        if ($this->canUnhold() || $this->isPaymentReview()) {
            return false;
        }

        if ($this->getIsVirtual() || $this->isCanceled()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_SHIP) === false) {
            return false;
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQtyToShip()>0 && !$item->getIsVirtual()
                && !$item->getLockedDoShip())
            {
                return true;
            }
        }
        return false;
    }

    /**
     * Retrieve order edit availability
     *
     * @return bool
     */
    public function canEdit()
    {
        if ($this->canUnhold()) {
            return false;
        }

        $state = $this->getState();
        if ($this->isCanceled() || $this->isPaymentReview()
            || $state === self::STATE_COMPLETE || $state === self::STATE_CLOSED) {
            return false;
        }

        if (!$this->getPayment()->getMethodInstance()->canEdit()) {
            return false;
        }

        if ($this->getActionFlag(self::ACTION_FLAG_EDIT) === false) {
            return false;
        }

        return true;
    }

    /**
     * Retrieve order reorder availability
     *
     * @return bool
     */
    public function canReorder()
    {
        return $this->_canReorder(false);
    }

    /**
     * Check the ability to reorder ignoring the availability in stock or status of the ordered products
     *
     * @return bool
     */
    public function canReorderIgnoreSalable()
    {
        return $this->_canReorder(true);
    }

    /**
     * Retrieve order reorder availability
     *
     * @param bool $ignoreSalable
     * @return bool
     */
    protected function _canReorder($ignoreSalable = false)
    {
        if ($this->canUnhold() || $this->isPaymentReview() || !$this->getCustomerId()) {
            return false;
        }

        $products = array();
        foreach ($this->getItemsCollection() as $item) {
            $products[] = $item->getProductId();
        }

        if (!empty($products)) {
            /*
             * @TODO ACPAOC: Use product collection here, but ensure that product
             * is loaded with order store id, otherwise there'll be problems with isSalable()
             * for configurables, bundles and other composites
             *
             */
            /*
            $productsCollection = \Mage::getModel('Magento\Catalog\Model\Product')->getCollection()
                ->setStoreId($this->getStoreId())
                ->addIdFilter($products)
                ->addAttributeToSelect('status')
                ->load();

            foreach ($productsCollection as $product) {
                if (!$product->isSalable()) {
                    return false;
                }
            }
            */

            foreach ($products as $productId) {
                $product = \Mage::getModel('Magento\Catalog\Model\Product')
                    ->setStoreId($this->getStoreId())
                    ->load($productId);
                if (!$product->getId() || (!$ignoreSalable && !$product->isSalable())) {
                    return false;
                }
            }
        }

        if ($this->getActionFlag(self::ACTION_FLAG_REORDER) === false) {
            return false;
        }

        return true;
    }

    /**
     * Check whether the payment is in payment review state
     * In this state order cannot be normally processed. Possible actions can be:
     * - accept or deny payment
     * - fetch transaction information
     *
     * @return bool
     */
    public function isPaymentReview()
    {
        return $this->getState() === self::STATE_PAYMENT_REVIEW;
    }

    /**
     * Check whether payment can be accepted or denied
     *
     * @return bool
     */
    public function canReviewPayment()
    {
        return $this->isPaymentReview() && $this->getPayment()->canReviewPayment();
    }

    /**
     * Check whether there can be a transaction update fetched for payment in review state
     *
     * @return bool
     */
    public function canFetchPaymentReviewUpdate()
    {
        return $this->isPaymentReview() && $this->getPayment()->canFetchTransactionInfo();
    }

    /**
     * Retrieve order configuration model
     *
     * @return \Magento\Sales\Model\Order\Config
     */
    public function getConfig()
    {
        return \Mage::getSingleton('Magento\Sales\Model\Order\Config');
    }

    /**
     * Place order payments
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _placePayment()
    {
        $this->getPayment()->place();
        return $this;
    }

    /**
     * Retrieve order payment model object
     *
     * @return \Magento\Sales\Model\Order\Payment
     */
    public function getPayment()
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                return $payment;
            }
        }
        return false;
    }

    /**
     * Declare order billing address
     *
     * @param   \Magento\Sales\Model\Order\Address $address
     * @return  \Magento\Sales\Model\Order
     */
    public function setBillingAddress(\Magento\Sales\Model\Order\Address $address)
    {
        $old = $this->getBillingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('billing'));
        return $this;
    }

    /**
     * Declare order shipping address
     *
     * @param   \Magento\Sales\Model\Order\Address $address
     * @return  \Magento\Sales\Model\Order
     */
    public function setShippingAddress(\Magento\Sales\Model\Order\Address $address)
    {
        $old = $this->getShippingAddress();
        if (!empty($old)) {
            $address->setId($old->getId());
        }
        $this->addAddress($address->setAddressType('shipping'));
        return $this;
    }

    /**
     * Retrieve order billing address
     *
     * @return \Magento\Sales\Model\Order\Address
     */
    public function getBillingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType()=='billing' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Retrieve order shipping address
     *
     * @return \Magento\Sales\Model\Order\Address|bool
     */
    public function getShippingAddress()
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getAddressType() == 'shipping' && !$address->isDeleted()) {
                return $address;
            }
        }
        return false;
    }

    /**
     * Order state setter.
     * If status is specified, will add order status history with specified comment
     * the setData() cannot be overriden because of compatibility issues with resource model
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @return \Magento\Sales\Model\Order
     */
    public function setState($state, $status = false, $comment = '', $isCustomerNotified = null)
    {
        return $this->_setState($state, $status, $comment, $isCustomerNotified, true);
    }

    /**
     * Order state protected setter.
     * By default allows to set any state. Can also update status to default or specified value
     * Сomplete and closed states are encapsulated intentionally, see the _checkState()
     *
     * @param string $state
     * @param string|bool $status
     * @param string $comment
     * @param bool $isCustomerNotified
     * @param $shouldProtectState
     * @return \Magento\Sales\Model\Order
     */
    protected function _setState($state, $status = false, $comment = '',
        $isCustomerNotified = null, $shouldProtectState = false)
    {
        // attempt to set the specified state
        if ($shouldProtectState) {
            if ($this->isStateProtected($state)) {
                \Mage::throwException(
                    __('The Order State "%1" must not be set manually.', $state)
                );
            }
        }
        $this->setData('state', $state);

        // add status history
        if ($status) {
            if ($status === true) {
                $status = $this->getConfig()->getStateDefaultStatus($state);
            }
            $this->setStatus($status);
            $history = $this->addStatusHistoryComment($comment, false); // no sense to set $status again
            $history->setIsCustomerNotified($isCustomerNotified); // for backwards compatibility
        }
        return $this;
    }

    /**
     * Whether specified state can be set from outside
     * @param $state
     * @return bool
     */
    public function isStateProtected($state)
    {
        if (empty($state)) {
            return false;
        }
        return self::STATE_COMPLETE == $state || self::STATE_CLOSED == $state;
    }

    /**
     * Retrieve label of order status
     *
     * @return string
     */
    public function getStatusLabel()
    {
        return $this->getConfig()->getStatusLabel($this->getStatus());
    }

    /**
     * Add status change information to history
     *
     * @param  string $status
     * @param  string $comment
     * @param  bool $isCustomerNotified
     * @return \Magento\Sales\Model\Order
     */
    public function addStatusToHistory($status, $comment = '', $isCustomerNotified = false)
    {
        $history = $this->addStatusHistoryComment($comment, $status)
            ->setIsCustomerNotified($isCustomerNotified);
        return $this;
    }

    /*
     * Add a comment to order
     * Different or default status may be specified
     *
     * @param string $comment
     * @param string $status
     * @return \Magento\Sales\Model\Order\Status\History
     */
    public function addStatusHistoryComment($comment, $status = false)
    {
        if (false === $status) {
            $status = $this->getStatus();
        } elseif (true === $status) {
            $status = $this->getConfig()->getStateDefaultStatus($this->getState());
        } else {
            $this->setStatus($status);
        }
        $history = \Mage::getModel('Magento\Sales\Model\Order\Status\History')
            ->setStatus($status)
            ->setComment($comment)
            ->setEntityName($this->_historyEntityName);
        $this->addStatusHistory($history);
        return $history;
    }

    /**
     * Overrides entity id, which will be saved to comments history status
     *
     * @param string $status
     * @return \Magento\Sales\Model\Order
     */
    public function setHistoryEntityName( $entityName )
    {
        $this->_historyEntityName = $entityName;
        return $this;
    }

    /**
     * Place order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function place()
    {
        \Mage::dispatchEvent('sales_order_place_before', array('order'=>$this));
        $this->_placePayment();
        \Mage::dispatchEvent('sales_order_place_after', array('order'=>$this));
        return $this;
    }

    public function hold()
    {
        if (!$this->canHold()) {
            \Mage::throwException(__('A hold action is not available.'));
        }
        $this->setHoldBeforeState($this->getState());
        $this->setHoldBeforeStatus($this->getStatus());
        $this->setState(self::STATE_HOLDED, true);
        return $this;
    }

    /**
     * Attempt to unhold the order
     *
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Core\Exception
     */
    public function unhold()
    {
        if (!$this->canUnhold()) {
            \Mage::throwException(__('You cannot remove the hold.'));
        }
        $this->setState($this->getHoldBeforeState(), $this->getHoldBeforeStatus());
        $this->setHoldBeforeState(null);
        $this->setHoldBeforeStatus(null);
        return $this;
    }

    /**
     * Cancel order
     *
     * @return \Magento\Sales\Model\Order
     */
    public function cancel()
    {
        if ($this->canCancel()) {
            $this->getPayment()->cancel();
            $this->registerCancellation();

            \Mage::dispatchEvent('order_cancel_after', array('order' => $this));
        }

        return $this;
    }

    /**
     * Prepare order totals to cancellation
     * @param string $comment
     * @param bool $graceful
     * @return \Magento\Sales\Model\Order
     * @throws \Magento\Core\Exception
     */
    public function registerCancellation($comment = '', $graceful = true)
    {
        if ($this->canCancel() || $this->isPaymentReview()) {
            $cancelState = self::STATE_CANCELED;
            foreach ($this->getAllItems() as $item) {
                if ($cancelState != self::STATE_PROCESSING && $item->getQtyToRefund()) {
                    if ($item->getQtyToShip() > $item->getQtyToCancel()) {
                        $cancelState = self::STATE_PROCESSING;
                    } else {
                        $cancelState = self::STATE_COMPLETE;
                    }
                }
                $item->cancel();
            }

            $this->setSubtotalCanceled($this->getSubtotal() - $this->getSubtotalInvoiced());
            $this->setBaseSubtotalCanceled($this->getBaseSubtotal() - $this->getBaseSubtotalInvoiced());

            $this->setTaxCanceled($this->getTaxAmount() - $this->getTaxInvoiced());
            $this->setBaseTaxCanceled($this->getBaseTaxAmount() - $this->getBaseTaxInvoiced());

            $this->setShippingCanceled($this->getShippingAmount() - $this->getShippingInvoiced());
            $this->setBaseShippingCanceled($this->getBaseShippingAmount() - $this->getBaseShippingInvoiced());

            $this->setDiscountCanceled(abs($this->getDiscountAmount()) - $this->getDiscountInvoiced());
            $this->setBaseDiscountCanceled(abs($this->getBaseDiscountAmount()) - $this->getBaseDiscountInvoiced());

            $this->setTotalCanceled($this->getGrandTotal() - $this->getTotalPaid());
            $this->setBaseTotalCanceled($this->getBaseGrandTotal() - $this->getBaseTotalPaid());

            $this->_setState($cancelState, true, $comment);
        } elseif (!$graceful) {
            \Mage::throwException(__('We cannot cancel this order.'));
        }
        return $this;
    }

    /**
     * Retrieve tracking numbers
     *
     * @return array
     */
    public function getTrackingNumbers()
    {
        if ($this->getData('tracking_numbers')) {
            return explode(',', $this->getData('tracking_numbers'));
        }
        return array();
    }

    /**
     * Return model of shipping carrier
     *
     * @return bool|float|\Magento\Shipping\Model\Carrier\AbstractCarrier
     */
    public function getShippingCarrier()
    {
        $carrierModel = $this->getData('shipping_carrier');
        if (is_null($carrierModel)) {
            $carrierModel = false;
            /**
             * $method - carrier_method
             */
            $method = $this->getShippingMethod(true);
            if ($method instanceof \Magento\Object) {
                $className = \Mage::getStoreConfig('carriers/' . $method->getCarrierCode() . '/model');
                if ($className) {
                    $carrierModel = \Mage::getModel($className);
                }
            }
            $this->setData('shipping_carrier', $carrierModel);
        }
        return $carrierModel;
    }

    /**
     * Retrieve shipping method
     *
     * @param bool $asObject return carrier code and shipping method data as object
     * @return string|\Magento\Object
     */
    public function getShippingMethod($asObject = false)
    {
        $shippingMethod = parent::getShippingMethod();
        if (!$asObject) {
            return $shippingMethod;
        } else {
            list($carrierCode, $method) = explode('_', $shippingMethod, 2);
            return new \Magento\Object(array(
                'carrier_code' => $carrierCode,
                'method'       => $method
            ));
        }
    }

    /**
     * Send email with order data
     *
     * @return \Magento\Sales\Model\Order
     */
    public function sendNewOrderEmail()
    {
        $storeId = $this->getStore()->getId();

        if (!\Mage::helper('Magento\Sales\Helper\Data')->canSendNewOrderEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = \Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $storeId);

        $paymentBlockHtml = \Mage::helper('Magento\Payment\Helper\Data')->getInfoBlockHtml($this->getPayment(), $storeId);

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = \Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = \Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = \Mage::getModel('Magento\Core\Model\Email\Template\Mailer');
        $emailInfo = \Mage::getModel('Magento\Core\Model\Email\Info');
        $emailInfo->addTo($this->getCustomerEmail(), $customerName);
        if ($copyTo && $copyMethod == 'bcc') {
            // Add bcc to customer email
            foreach ($copyTo as $email) {
                $emailInfo->addBcc($email);
            }
        }
        $mailer->addEmailInfo($emailInfo);

        // Email copies are sent as separated emails if their copy method is 'copy'
        if ($copyTo && $copyMethod == 'copy') {
            foreach ($copyTo as $email) {
                $emailInfo = \Mage::getModel('Magento\Core\Model\Email\Info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(\Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'        => $this,
                'billing'      => $this->getBillingAddress(),
                'payment_html' => $paymentBlockHtml
            )
        );
        $mailer->send();

        $this->setEmailSent(true);
        $this->_getResource()->saveAttribute($this, 'email_sent');

        return $this;
    }

    /**
     * Send email with order update information
     *
     * @param boolean $notifyCustomer
     * @param string $comment
     * @return \Magento\Sales\Model\Order
     */
    public function sendOrderUpdateEmail($notifyCustomer = true, $comment = '')
    {
        $storeId = $this->getStore()->getId();

        if (!\Mage::helper('Magento\Sales\Helper\Data')->canSendOrderCommentEmail($storeId)) {
            return $this;
        }
        // Get the destination email addresses to send copies to
        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = \Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $storeId);
        // Check if at least one recepient is found
        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        // Retrieve corresponding email template id and customer name
        if ($this->getCustomerIsGuest()) {
            $templateId = \Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $storeId);
            $customerName = $this->getBillingAddress()->getName();
        } else {
            $templateId = \Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $storeId);
            $customerName = $this->getCustomerName();
        }

        $mailer = \Mage::getModel('Magento\Core\Model\Email\Template\Mailer');
        if ($notifyCustomer) {
            $emailInfo = \Mage::getModel('Magento\Core\Model\Email\Info');
            $emailInfo->addTo($this->getCustomerEmail(), $customerName);
            if ($copyTo && $copyMethod == 'bcc') {
                // Add bcc to customer email
                foreach ($copyTo as $email) {
                    $emailInfo->addBcc($email);
                }
            }
            $mailer->addEmailInfo($emailInfo);
        }

        // Email copies are sent as separated emails if their copy method is
        // 'copy' or a customer should not be notified
        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $emailInfo = \Mage::getModel('Magento\Core\Model\Email\Info');
                $emailInfo->addTo($email);
                $mailer->addEmailInfo($emailInfo);
            }
        }

        // Set all required params and send emails
        $mailer->setSender(\Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $storeId));
        $mailer->setStoreId($storeId);
        $mailer->setTemplateId($templateId);
        $mailer->setTemplateParams(array(
                'order'   => $this,
                'comment' => $comment,
                'billing' => $this->getBillingAddress()
            )
        );
        $mailer->send();

        return $this;
    }

    protected function _getEmails($configPath)
    {
        $data = \Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

/*********************** ADDRESSES ***************************/

    /**
     * @return \Magento\Sales\Model\Resource\Order\Address\Collection
     */
    public function getAddressesCollection()
    {
        if (is_null($this->_addresses)) {
            $this->_addresses = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Address\Collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_addresses as $address) {
                    $address->setOrder($this);
                }
            }
        }

        return $this->_addresses;
    }

    public function getAddressById($addressId)
    {
        foreach ($this->getAddressesCollection() as $address) {
            if ($address->getId()==$addressId) {
                return $address;
            }
        }
        return false;
    }

    public function addAddress(\Magento\Sales\Model\Order\Address $address)
    {
        $address->setOrder($this)->setParentId($this->getId());
        if (!$address->getId()) {
            $this->getAddressesCollection()->addItem($address);
            $this->setDataChanges(true);
        }
        return $this;
    }

    /**
     * @param array $filterByTypes
     * @param bool $nonChildrenOnly
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    public function getItemsCollection($filterByTypes = array(), $nonChildrenOnly = false)
    {
        if (is_null($this->_items)) {
            $this->_items = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Item\Collection')
                ->setOrderFilter($this);

            if ($filterByTypes) {
                $this->_items->filterByTypes($filterByTypes);
            }
            if ($nonChildrenOnly) {
                $this->_items->filterByParent();
            }

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setOrder($this);
                }
            }
        }
        return $this->_items;
    }

    /**
     * Get random items collection with related children
     *
     * @param int $limit
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    public function getItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit);
    }

    /**
     * Get random items collection without related children
     *
     * @param int $limit
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    public function getParentItemsRandomCollection($limit = 1)
    {
        return $this->_getItemsRandomCollection($limit, true);
    }

    /**
     * Get random items collection with or without related children
     *
     * @param int $limit
     * @param bool $nonChildrenOnly
     * @return \Magento\Sales\Model\Resource\Order\Item\Collection
     */
    protected function _getItemsRandomCollection($limit, $nonChildrenOnly = false)
    {
        $collection = \Mage::getModel('Magento\Sales\Model\Order\Item')->getCollection()
            ->setOrderFilter($this)
            ->setRandomOrder();

        if ($nonChildrenOnly) {
            $collection->filterByParent();
        }
        $products = array();
        foreach ($collection as $item) {
            $products[] = $item->getProductId();
        }

        $productsCollection = \Mage::getModel('Magento\Catalog\Model\Product')
            ->getCollection()
            ->addIdFilter($products)
            ->setVisibility(\Mage::getSingleton('Magento\Catalog\Model\Product\Visibility')->getVisibleInSiteIds())
            /* Price data is added to consider item stock status using price index */
            ->addPriceData()
            ->setPageSize($limit)
            ->load();

        foreach ($collection as $item) {
            $product = $productsCollection->getItemById($item->getProductId());
            if ($product) {
                $item->setProduct($product);
            }
        }

        return $collection;
    }

    public function getAllItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getAllVisibleItems()
    {
        $items = array();
        foreach ($this->getItemsCollection() as $item) {
            if (!$item->isDeleted() && !$item->getParentItemId()) {
                $items[] =  $item;
            }
        }
        return $items;
    }

    public function getItemById($itemId)
    {
        return $this->getItemsCollection()->getItemById($itemId);
    }

    public function getItemByQuoteItemId($quoteItemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getQuoteItemId()==$quoteItemId) {
                return $item;
            }
        }
        return null;
    }

    public function addItem(\Magento\Sales\Model\Order\Item $item)
    {
        $item->setOrder($this);
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Whether the order has nominal items only
     *
     * @return bool
     */
    public function isNominal()
    {
        foreach ($this->getAllVisibleItems() as $item) {
            if ('0' == $item->getIsNominal()) {
                return false;
            }
        }
        return true;
    }

/*********************** PAYMENTS ***************************/

    /**
     * @return \Magento\Sales\Model\Resource\Order\Payment\Collection
     */
    public function getPaymentsCollection()
    {
        if (is_null($this->_payments)) {
            $this->_payments = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Payment\Collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_payments as $payment) {
                    $payment->setOrder($this);
                }
            }
        }
        return $this->_payments;
    }

    public function getAllPayments()
    {
        $payments = array();
        foreach ($this->getPaymentsCollection() as $payment) {
            if (!$payment->isDeleted()) {
                $payments[] =  $payment;
            }
        }
        return $payments;
    }


    public function getPaymentById($paymentId)
    {
        foreach ($this->getPaymentsCollection() as $payment) {
            if ($payment->getId()==$paymentId) {
                return $payment;
            }
        }
        return false;
    }

    public function addPayment(\Magento\Sales\Model\Order\Payment $payment)
    {
        $payment->setOrder($this)
            ->setParentId($this->getId());
        if (!$payment->getId()) {
            $this->getPaymentsCollection()->addItem($payment);
            $this->setDataChanges(true);
        }
        return $this;
    }

    public function setPayment(\Magento\Sales\Model\Order\Payment $payment)
    {
        if (!$this->getIsMultiPayment() && ($old = $this->getPayment())) {
            $payment->setId($old->getId());
        }
        $this->addPayment($payment);
        return $payment;
    }

/*********************** STATUSES ***************************/

    /**
     * Return collection of order status history items.
     *
     * @return \Magento\Sales\Model\Resource\Order\Status\History\Collection
     */
    public function getStatusHistoryCollection($reload = false)
    {
        if (is_null($this->_statusHistory) || $reload) {
            $this->_statusHistory = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Status\History\Collection')
                ->setOrderFilter($this)
                ->setOrder('created_at', 'desc')
                ->setOrder('entity_id', 'desc');

            if ($this->getId()) {
                foreach ($this->_statusHistory as $status) {
                    $status->setOrder($this);
                }
            }
        }
        return $this->_statusHistory;
    }

    /**
     * Return array of order status history items without deleted.
     *
     * @return array
     */
    public function getAllStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    /**
     * Return collection of visible on frontend order status history items.
     *
     * @return array
     */
    public function getVisibleStatusHistory()
    {
        $history = array();
        foreach ($this->getStatusHistoryCollection() as $status) {
            if (!$status->isDeleted() && $status->getComment() && $status->getIsVisibleOnFront()) {
                $history[] =  $status;
            }
        }
        return $history;
    }

    public function getStatusHistoryById($statusId)
    {
        foreach ($this->getStatusHistoryCollection() as $status) {
            if ($status->getId()==$statusId) {
                return $status;
            }
        }
        return false;
    }

    /**
     * Set the order status history object and the order object to each other
     * Adds the object to the status history collection, which is automatically saved when the order is saved.
     * See the entity_id attribute backend model.
     * Or the history record can be saved standalone after this.
     *
     * @param \Magento\Sales\Model\Order\Status\History $status
     * @return \Magento\Sales\Model\Order
     */
    public function addStatusHistory(\Magento\Sales\Model\Order\Status\History $history)
    {
        $history->setOrder($this);
        $this->setStatus($history->getStatus());
        if (!$history->getId()) {
            $this->getStatusHistoryCollection()->addItem($history);
            $this->setDataChanges(true);
        }
        return $this;
    }


    /**
     * Enter description here...
     *
     * @return string
     */
    public function getRealOrderId()
    {
        $id = $this->getData('real_order_id');
        if (is_null($id)) {
            $id = $this->getIncrementId();
        }
        return $id;
    }

    /**
     * Get currency model instance. Will be used currency with which order placed
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getOrderCurrency()
    {
        if (is_null($this->_orderCurrency)) {
            $this->_orderCurrency = \Mage::getModel('Magento\Directory\Model\Currency');
            $this->_orderCurrency->load($this->getOrderCurrencyCode());
        }
        return $this->_orderCurrency;
    }

    /**
     * Get formated price value including order currency rate to order website currency
     *
     * @param   float $price
     * @param   bool  $addBrackets
     * @return  string
     */
    public function formatPrice($price, $addBrackets = false)
    {
        return $this->formatPricePrecision($price, 2, $addBrackets);
    }

    public function formatPricePrecision($price, $precision, $addBrackets = false)
    {
        return $this->getOrderCurrency()->formatPrecision($price, $precision, array(), true, $addBrackets);
    }

    /**
     * Retrieve text formated price value includeing order rate
     *
     * @param   float $price
     * @return  string
     */
    public function formatPriceTxt($price)
    {
        return $this->getOrderCurrency()->formatTxt($price);
    }

    /**
     * Retrieve order website currency for working with base prices
     *
     * @return \Magento\Directory\Model\Currency
     */
    public function getBaseCurrency()
    {
        if (is_null($this->_baseCurrency)) {
            $this->_baseCurrency = \Mage::getModel('Magento\Directory\Model\Currency')->load($this->getBaseCurrencyCode());
        }
        return $this->_baseCurrency;
    }

    public function formatBasePrice($price)
    {
        return $this->formatBasePricePrecision($price, 2);
    }

    public function formatBasePricePrecision($price, $precision)
    {
        return $this->getBaseCurrency()->formatPrecision($price, $precision);
    }

    public function isCurrencyDifferent()
    {
        return $this->getOrderCurrencyCode() != $this->getBaseCurrencyCode();
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getTotalDue()
    {
        $total = $this->getGrandTotal()-$this->getTotalPaid();
        $total = \Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    /**
     * Retrieve order total due value
     *
     * @return float
     */
    public function getBaseTotalDue()
    {
        $total = $this->getBaseGrandTotal()-$this->getBaseTotalPaid();
        $total = \Mage::app()->getStore($this->getStoreId())->roundPrice($total);
        return max($total, 0);
    }

    public function getData($key='', $index=null)
    {
        if ($key == 'total_due') {
            return $this->getTotalDue();
        }
        if ($key == 'base_total_due') {
            return $this->getBaseTotalDue();
        }
        return parent::getData($key, $index);
    }

    /**
     * Retrieve order invoices collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Invoice\Collection
     */
    public function getInvoiceCollection()
    {
        if (is_null($this->_invoices)) {
            $this->_invoices = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Invoice\Collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                foreach ($this->_invoices as $invoice) {
                    $invoice->setOrder($this);
                }
            }
        }
        return $this->_invoices;
    }

    /**
     * Retrieve order shipments collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Shipment\Collection|bool
     */
    public function getShipmentsCollection()
    {
        if (empty($this->_shipments)) {
            if ($this->getId()) {
                $this->_shipments = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Shipment\Collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }
        return $this->_shipments;
    }

    /**
     * Retrieve order creditmemos collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Creditmemo\Collection|bool
     */
    public function getCreditmemosCollection()
    {
        if (empty($this->_creditmemos)) {
            if ($this->getId()) {
                $this->_creditmemos = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Creditmemo\Collection')
                    ->setOrderFilter($this)
                    ->load();
            } else {
                return false;
            }
        }
        return $this->_creditmemos;
    }

    /**
     * Retrieve order tracking numbers collection
     *
     * @return \Magento\Sales\Model\Resource\Order\Shipment\Track\Collection
     */
    public function getTracksCollection()
    {
        if (empty($this->_tracks)) {
            $this->_tracks = \Mage::getResourceModel('Magento\Sales\Model\Resource\Order\Shipment\Track\Collection')
                ->setOrderFilter($this);

            if ($this->getId()) {
                $this->_tracks->load();
            }
        }
        return $this->_tracks;
    }

    /**
     * Check order invoices availability
     *
     * @return bool
     */
    public function hasInvoices()
    {
        return $this->getInvoiceCollection()->count();
    }

    /**
     * Check order shipments availability
     *
     * @return bool
     */
    public function hasShipments()
    {
        return $this->getShipmentsCollection()->count();
    }

    /**
     * Check order creditmemos availability
     *
     * @return bool
     */
    public function hasCreditmemos()
    {
        return $this->getCreditmemosCollection()->count();
    }


    /**
     * Retrieve array of related objects
     *
     * Used for order saving
     *
     * @return array
     */
    public function getRelatedObjects()
    {
        return $this->_relatedObjects;
    }

    public function getCustomerName()
    {
        if ($this->getCustomerFirstname()) {
            $customerName = $this->getCustomerFirstname() . ' ' . $this->getCustomerLastname();
        }
        else {
            $customerName = __('Guest');
        }
        return $customerName;
    }

    /**
     * Add New object to related array
     *
     * @param   \Magento\Core\Model\AbstractModel $object
     * @return  \Magento\Sales\Model\Order
     */
    public function addRelatedObject(\Magento\Core\Model\AbstractModel $object)
    {
        $this->_relatedObjects[] = $object;
        return $this;
    }

    /**
     * Get formated order created date in store timezone
     *
     * @param   string $format date format type (short|medium|long|full)
     * @return  string
     */
    public function getCreatedAtFormated($format)
    {
        return \Mage::helper('Magento\Core\Helper\Data')->formatDate($this->getCreatedAtStoreDate(), $format, true);
    }

    public function getEmailCustomerNote()
    {
        if ($this->getCustomerNoteNotify()) {
            return $this->getCustomerNote();
        }
        return '';
    }

    /**
     * Processing object before save data
     *
     * @return \Magento\Core\Model\AbstractModel
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();
        $this->_checkState();
        if (!$this->getId()) {
            $store = $this->getStore();
            $name = array($store->getWebsite()->getName(), $store->getGroup()->getName(), $store->getName());
            $this->setStoreName(implode("\n", $name));
        }

        if (!$this->getIncrementId()) {
            $incrementId = \Mage::getSingleton('Magento\Eav\Model\Config')
                ->getEntityType('order')
                ->fetchNewIncrementId($this->getStoreId());
            $this->setIncrementId($incrementId);
        }

        /**
         * Process items dependency for new order
         */
        if (!$this->getId()) {
            $itemsCount = 0;
            foreach ($this->getAllItems() as $item) {
                $parent = $item->getQuoteParentItemId();
                if ($parent && !$item->getParentItem()) {
                    $item->setParentItem($this->getItemByQuoteItemId($parent));
                } elseif (!$parent) {
                    $itemsCount++;
                }
            }
            // Set items count
            $this->setTotalItemCount($itemsCount);
        }
        if ($this->getCustomer()) {
            $this->setCustomerId($this->getCustomer()->getId());
        }

        if ($this->hasBillingAddressId() && $this->getBillingAddressId() === null) {
            $this->unsBillingAddressId();
        }

        if ($this->hasShippingAddressId() && $this->getShippingAddressId() === null) {
            $this->unsShippingAddressId();
        }

        $this->setData('protect_code', substr(md5(uniqid(mt_rand(), true) . ':' . microtime(true)), 5, 6));
        return $this;
    }

    /**
     * Check order state before saving
     */
    protected function _checkState()
    {
        if (!$this->getId()) {
            return $this;
        }

        $userNotification = $this->hasCustomerNoteNotify() ? $this->getCustomerNoteNotify() : null;

        if (!$this->isCanceled()
            && !$this->canUnhold()
            && !$this->canInvoice()
            && !$this->canShip()) {
            if (0 == $this->getBaseGrandTotal() || $this->canCreditmemo()) {
                if ($this->getState() !== self::STATE_COMPLETE) {
                    $this->_setState(self::STATE_COMPLETE, true, '', $userNotification);
                }
            }
            /**
             * Order can be closed just in case when we have refunded amount.
             * In case of "0" grand total order checking ForcedCanCreditmemo flag
             */
            elseif (floatval($this->getTotalRefunded()) || (!$this->getTotalRefunded()
                && $this->hasForcedCanCreditmemo())
            ) {
                if ($this->getState() !== self::STATE_CLOSED) {
                    $this->_setState(self::STATE_CLOSED, true, '', $userNotification);
                }
            }
        }

        if ($this->getState() == self::STATE_NEW && $this->getIsInProcess()) {
            $this->setState(self::STATE_PROCESSING, true, '', $userNotification);
        }
        return $this;
    }

    /**
     * Save order related objects
     *
     * @return \Magento\Sales\Model\Order
     */
    protected function _afterSave()
    {
        if (null !== $this->_addresses) {
            $this->_addresses->save();
            $billingAddress = $this->getBillingAddress();
            $attributesForSave = array();
            if ($billingAddress && $this->getBillingAddressId() != $billingAddress->getId()) {
                $this->setBillingAddressId($billingAddress->getId());
                $attributesForSave[] = 'billing_address_id';
            }

            $shippingAddress = $this->getShippingAddress();
            if ($shippingAddress && $this->getShippigAddressId() != $shippingAddress->getId()) {
                $this->setShippingAddressId($shippingAddress->getId());
                $attributesForSave[] = 'shipping_address_id';
            }

            if (!empty($attributesForSave)) {
                $this->_getResource()->saveAttribute($this, $attributesForSave);
            }

        }
        if (null !== $this->_items) {
            $this->_items->save();
        }
        if (null !== $this->_payments) {
            $this->_payments->save();
        }
        if (null !== $this->_statusHistory) {
            $this->_statusHistory->save();
        }
        foreach ($this->getRelatedObjects() as $object) {
            $object->save();
        }
        return parent::_afterSave();
    }

    public function getStoreGroupName()
    {
        $storeId = $this->getStoreId();
        if (is_null($storeId)) {
            return $this->getStoreName(1); // 0 - website name, 1 - store group name, 2 - store name
        }
        return $this->getStore()->getGroup()->getName();
    }

    /**
     * Resets all data in object
     * so after another load it will be complete new object
     *
     * @return \Magento\Sales\Model\Order
     */
    public function reset()
    {
        $this->unsetData();
        $this->_actionFlag = array();
        $this->_addresses = null;
        $this->_items = null;
        $this->_payments = null;
        $this->_statusHistory = null;
        $this->_invoices = null;
        $this->_tracks = null;
        $this->_shipments = null;
        $this->_creditmemos = null;
        $this->_relatedObjects = array();
        $this->_orderCurrency = null;
        $this->_baseCurrency = null;

        return $this;
    }

    public function getIsNotVirtual()
    {
        return !$this->getIsVirtual();
    }

    public function getFullTaxInfo()
    {
        $rates = \Mage::getModel('Magento\Tax\Model\Sales\Order\Tax')->getCollection()->loadByOrder($this)->toArray();
        return \Mage::getSingleton('Magento\Tax\Model\Calculation')->reproduceProcess($rates['items']);
    }

    /**
     * Create new invoice with maximum qty for invoice for each item
     *
     * @return \Magento\Sales\Model\Order\Invoice
     */
    public function prepareInvoice($qtys = array())
    {
        $invoice = \Mage::getModel('Magento\Sales\Model\Service\Order', array('order' => $this))->prepareInvoice($qtys);
        return $invoice;
    }

    /**
     * Create new shipment with maximum qty for shipping for each item
     *
     * @return \Magento\Sales\Model\Order\Shipment
     */
    public function prepareShipment($qtys = array())
    {
        $shipment = \Mage::getModel('Magento\Sales\Model\Service\Order', array('order' => $this))->prepareShipment($qtys);
        return $shipment;
    }

    /**
     * Check whether order is canceled
     *
     * @return bool
     */
    public function isCanceled()
    {
        return ($this->getState() === self::STATE_CANCELED);
    }

    /**
     * Protect order delete from not admin scope
     * @return \Magento\Sales\Model\Order
     */
    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }
}
