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
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


/**
 * Enter description here ...
 *
 * @method Mage_Sales_Model_Resource_Order_Creditmemo _getResource()
 * @method Mage_Sales_Model_Resource_Order_Creditmemo getResource()
 * @method Mage_Sales_Model_Order_Creditmemo getStoreId()
 * @method int setStoreId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getAdjustmentPositive()
 * @method Mage_Sales_Model_Order_Creditmemo getBaseShippingTaxAmount()
 * @method float setBaseShippingTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getStoreToOrderRate()
 * @method float setStoreToOrderRate(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseDiscountAmount()
 * @method float setBaseDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseToOrderRate()
 * @method float setBaseToOrderRate(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getGrandTotal()
 * @method float setGrandTotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseAdjustmentNegative()
 * @method float setBaseAdjustmentNegative(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseSubtotalInclTax()
 * @method float setBaseSubtotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getShippingAmount()
 * @method Mage_Sales_Model_Order_Creditmemo getSubtotalInclTax()
 * @method float setSubtotalInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getAdjustmentNegative()
 * @method Mage_Sales_Model_Order_Creditmemo getBaseShippingAmount()
 * @method float setBaseShippingAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getStoreToBaseRate()
 * @method float setStoreToBaseRate(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseToGlobalRate()
 * @method float setBaseToGlobalRate(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseAdjustment()
 * @method float setBaseAdjustment(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseSubtotal()
 * @method float setBaseSubtotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getDiscountAmount()
 * @method float setDiscountAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getSubtotal()
 * @method float setSubtotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getAdjustment()
 * @method float setAdjustment(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseGrandTotal()
 * @method float setBaseGrandTotal(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseAdjustmentPositive()
 * @method float setBaseAdjustmentPositive(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseTaxAmount()
 * @method float setBaseTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getShippingTaxAmount()
 * @method float setShippingTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getTaxAmount()
 * @method float setTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getOrderId()
 * @method int setOrderId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getEmailSent()
 * @method int setEmailSent(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getCreditmemoStatus()
 * @method int setCreditmemoStatus(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getState()
 * @method int setState(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getShippingAddressId()
 * @method int setShippingAddressId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBillingAddressId()
 * @method int setBillingAddressId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getInvoiceId()
 * @method int setInvoiceId(int $value)
 * @method Mage_Sales_Model_Order_Creditmemo getCybersourceToken()
 * @method string setCybersourceToken(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getStoreCurrencyCode()
 * @method string setStoreCurrencyCode(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getOrderCurrencyCode()
 * @method string setOrderCurrencyCode(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseCurrencyCode()
 * @method string setBaseCurrencyCode(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getGlobalCurrencyCode()
 * @method string setGlobalCurrencyCode(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getTransactionId()
 * @method string setTransactionId(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getIncrementId()
 * @method string setIncrementId(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getCreatedAt()
 * @method string setCreatedAt(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getUpdatedAt()
 * @method string setUpdatedAt(string $value)
 * @method Mage_Sales_Model_Order_Creditmemo getHiddenTaxAmount()
 * @method float setHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseHiddenTaxAmount()
 * @method float setBaseHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getShippingHiddenTaxAmount()
 * @method float setShippingHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseShippingHiddenTaxAmount()
 * @method float setBaseShippingHiddenTaxAmount(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getShippingInclTax()
 * @method float setShippingInclTax(float $value)
 * @method Mage_Sales_Model_Order_Creditmemo getBaseShippingInclTax()
 * @method float setBaseShippingInclTax(float $value)
 *
 * @category    Mage
 * @package     Mage_Sales
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_Sales_Model_Order_Creditmemo extends Mage_Sales_Model_Abstract
{
    const STATE_OPEN        = 1;
    const STATE_REFUNDED    = 2;
    const STATE_CANCELED    = 3;

    const XML_PATH_EMAIL_TEMPLATE               = 'sales_email/creditmemo/template';
    const XML_PATH_EMAIL_GUEST_TEMPLATE         = 'sales_email/creditmemo/guest_template';
    const XML_PATH_EMAIL_IDENTITY               = 'sales_email/creditmemo/identity';
    const XML_PATH_EMAIL_COPY_TO                = 'sales_email/creditmemo/copy_to';
    const XML_PATH_EMAIL_COPY_METHOD            = 'sales_email/creditmemo/copy_method';
    const XML_PATH_EMAIL_ENABLED                = 'sales_email/creditmemo/enabled';

    const XML_PATH_UPDATE_EMAIL_TEMPLATE        = 'sales_email/creditmemo_comment/template';
    const XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE  = 'sales_email/creditmemo_comment/guest_template';
    const XML_PATH_UPDATE_EMAIL_IDENTITY        = 'sales_email/creditmemo_comment/identity';
    const XML_PATH_UPDATE_EMAIL_COPY_TO         = 'sales_email/creditmemo_comment/copy_to';
    const XML_PATH_UPDATE_EMAIL_COPY_METHOD     = 'sales_email/creditmemo_comment/copy_method';
    const XML_PATH_UPDATE_EMAIL_ENABLED         = 'sales_email/creditmemo_comment/enabled';

    const REPORT_DATE_TYPE_ORDER_CREATED        = 'order_created';
    const REPORT_DATE_TYPE_REFUND_CREATED       = 'refund_created';

    protected static $_states;

    protected $_items;
    protected $_order;
    protected $_comments;

    protected $_eventPrefix = 'sales_order_creditmemo';
    protected $_eventObject = 'creditmemo';

    /**
     * Initialize creditmemo resource model
     */
    protected function _construct()
    {
        $this->_init('sales/order_creditmemo');
    }

    /**
     * Retrieve Creditmemo configuration model
     *
     * @return Mage_Sales_Model_Order_Creditmemo_Config
     */
    public function getConfig()
    {
        return Mage::getSingleton('sales/order_creditmemo_config');
    }

    /**
     * Retrieve creditmemo store instance
     *
     * @return Mage_Core_Model_Store
     */
    public function getStore()
    {
        return $this->getOrder()->getStore();
    }

    /**
     * Declare order for creditmemo
     *
     * @param   Mage_Sales_Model_Order $order
     * @return  Mage_Sales_Model_Order_Creditmemo
     */
    public function setOrder(Mage_Sales_Model_Order $order)
    {
        $this->_order = $order;
        $this->setOrderId($order->getId())
            ->setStoreId($order->getStoreId());
        return $this;
    }

    /**
     * Retrieve the order the creditmemo for created for
     *
     * @return Mage_Sales_Model_Order
     */
    public function getOrder()
    {
        if (!$this->_order instanceof Mage_Sales_Model_Order) {
            $this->_order = Mage::getModel('sales/order')->load($this->getOrderId());
        }
        return $this->_order;
    }

    /**
     * Retrieve billing address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getBillingAddress()
    {
        return $this->getOrder()->getBillingAddress();
    }

    /**
     * Retrieve shipping address
     *
     * @return Mage_Sales_Model_Order_Address
     */
    public function getShippingAddress()
    {
        return $this->getOrder()->getShippingAddress();
    }

    public function getItemsCollection()
    {
        if (empty($this->_items)) {
            $this->_items = Mage::getResourceModel('sales/order_creditmemo_item_collection')
                ->setCreditmemoFilter($this->getId());

            if ($this->getId()) {
                foreach ($this->_items as $item) {
                    $item->setCreditmemo($this);
                }
            }
        }
        return $this->_items;
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

    public function getItemById($itemId)
    {
        foreach ($this->getItemsCollection() as $item) {
            if ($item->getId()==$itemId) {
                return $item;
            }
        }
        return false;
    }

    public function addItem(Mage_Sales_Model_Order_Creditmemo_Item $item)
    {
        $item->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$item->getId()) {
            $this->getItemsCollection()->addItem($item);
        }
        return $this;
    }

    /**
     * Creditmemo totals collecting
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function collectTotals()
    {
        foreach ($this->getConfig()->getTotalModels() as $model) {
            $model->collect($this);
        }
        return $this;
    }

    public function canRefund()
    {
        if ($this->getState() != self::STATE_CANCELED
            && $this->getState() != self::STATE_REFUNDED
            && $this->getOrder()->getPayment()->canRefund()
            ) {
            return true;
        }
        return false;
    }

    /**
     * Check creditmemo cancel action availability
     *
     * @return bool
     */
    public function canCancel()
    {
        return $this->getState() == self::STATE_OPEN;
    }

    /**
     * Check invice void action availability
     *
     * @return bool
     */
    public function canVoid()
    {
        $canVoid = false;
        return false;
        if ($this->getState() == self::STATE_REFUNDED) {
            $canVoid = $this->getCanVoidFlag();
            /**
             * If we not retrieve negative answer from payment yet
             */
            if (is_null($canVoid)) {
                $canVoid = $this->getOrder()->getPayment()->canVoid($this);
                if ($canVoid === false) {
                    $this->setCanVoidFlag(false);
                    $this->_saveBeforeDestruct = true;
                }
            }
            else {
                $canVoid = (bool) $canVoid;
            }
        }
        return $canVoid;
    }


    public function refund()
    {
        $this->setState(self::STATE_REFUNDED);
        $orderRefund = Mage::app()->getStore()->roundPrice($this->getOrder()->getTotalRefunded()+$this->getGrandTotal());
        $baseOrderRefund = Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseTotalRefunded()+$this->getBaseGrandTotal());

        if ($baseOrderRefund > Mage::app()->getStore()->roundPrice($this->getOrder()->getBaseTotalPaid())) {

            $baseAvailableRefund = $this->getOrder()->getBaseTotalPaid()- $this->getOrder()->getBaseTotalRefunded();

            Mage::throwException(
                Mage::helper('sales')->__('Maximum amount available to refund is %s',
                    $this->getOrder()->formatBasePrice($baseAvailableRefund)
                )
            );
        }
        $order = $this->getOrder();
        $order->setBaseTotalRefunded($baseOrderRefund);
        $order->setTotalRefunded($orderRefund);

        $order->setBaseSubtotalRefunded($order->getBaseSubtotalRefunded()+$this->getBaseSubtotal());
        $order->setSubtotalRefunded($order->getSubtotalRefunded()+$this->getSubtotal());

        $order->setBaseTaxRefunded($order->getBaseTaxRefunded()+$this->getBaseTaxAmount());
        $order->setTaxRefunded($order->getTaxRefunded()+$this->getTaxAmount());
        $order->setBaseHiddenTaxRefunded($order->getBaseHiddenTaxRefunded()+$this->getBaseHiddenTaxAmount());
        $order->setHiddenTaxRefunded($order->getHiddenTaxRefunded()+$this->getHiddenTaxAmount());

        $order->setBaseShippingRefunded($order->getBaseShippingRefunded()+$this->getBaseShippingAmount());
        $order->setShippingRefunded($order->getShippingRefunded()+$this->getShippingAmount());

        $order->setBaseShippingTaxRefunded($order->getBaseShippingTaxRefunded()+$this->getBaseShippingTaxAmount());
        $order->setShippingTaxRefunded($order->getShippingTaxRefunded()+$this->getShippingTaxAmount());

        $order->setAdjustmentPositive($order->getAdjustmentPositive()+$this->getAdjustmentPositive());
        $order->setBaseAdjustmentPositive($order->getBaseAdjustmentPositive()+$this->getBaseAdjustmentPositive());

        $order->setAdjustmentNegative($order->getAdjustmentNegative()+$this->getAdjustmentNegative());
        $order->setBaseAdjustmentNegative($order->getBaseAdjustmentNegative()+$this->getBaseAdjustmentNegative());

        $order->setDiscountRefunded($order->getDiscountRefunded()+$this->getDiscountAmount());
        $order->setBaseDiscountRefunded($order->getBaseDiscountRefunded()+$this->getBaseDiscountAmount());

        if ($this->getInvoice()) {
            $this->getInvoice()->setIsUsedForRefund(true);
            $this->setInvoiceId($this->getInvoice()->getId());
        }

        if (!$this->getPaymentRefundDisallowed()) {
            $order->getPayment()->refund($this);
        }

        Mage::dispatchEvent('sales_order_creditmemo_refund', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Cancel Creditmemo action
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function cancel()
    {
        $this->setState(self::STATE_CANCELED);
        foreach ($this->getAllItems() as $item) {
            $item->cancel();
        }
        $this->getOrder()->getPayment()->cancelCreditmemo($this);

        if ($this->getTransactionId()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded()-$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded()-$this->getBaseGrandTotal()
            );
        }
        else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded()-$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded()-$this->getBaseGrandTotal()
            );
        }

        $this->getOrder()->setBaseSubtotalRefunded($this->getOrder()->getBaseSubtotalRefunded()-$this->getBaseSubtotal());
        $this->getOrder()->setSubtotalRefunded($this->getOrder()->getSubtotalRefunded()-$this->getSubtotal());

        $this->getOrder()->setBaseTaxRefunded($this->getOrder()->getBaseTaxRefunded()-$this->getBaseTaxAmount());
        $this->getOrder()->setTaxRefunded($this->getOrder()->getTaxRefunded()-$this->getTaxAmount());

        $this->getOrder()->setBaseShippingRefunded($this->getOrder()->getBaseShippingRefunded()-$this->getBaseShippingAmount());
        $this->getOrder()->setShippingRefunded($this->getOrder()->getShippingRefunded()-$this->getShippingAmount());

        Mage::dispatchEvent('sales_order_creditmemo_cancel', array($this->_eventObject=>$this));
        return $this;
    }

    /**
     * Register creditmemo
     *
     * Apply to order, order items etc.
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function register()
    {
        if ($this->getId()) {
            Mage::throwException(
                Mage::helper('sales')->__('Cannot register an existing credit memo.')
            );
        }

        foreach ($this->getAllItems() as $item) {
            if ($item->getQty()>0) {
                $item->register();
            }
            else {
                $item->isDeleted(true);
            }
        }

        $this->setDoTransaction(true);
        if ($this->getOfflineRequested()) {
            $this->setDoTransaction(false);
        }
        $this->refund();

        if ($this->getDoTransaction()) {
            $this->getOrder()->setTotalOnlineRefunded(
                $this->getOrder()->getTotalOnlineRefunded()+$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOnlineRefunded(
                $this->getOrder()->getBaseTotalOnlineRefunded()+$this->getBaseGrandTotal()
            );
        }
        else {
            $this->getOrder()->setTotalOfflineRefunded(
                $this->getOrder()->getTotalOfflineRefunded()+$this->getGrandTotal()
            );
            $this->getOrder()->setBaseTotalOfflineRefunded(
                $this->getOrder()->getBaseTotalOfflineRefunded()+$this->getBaseGrandTotal()
            );
        }

        $this->getOrder()->setBaseTotalInvoicedCost(
            $this->getOrder()->getBaseTotalInvoicedCost()-$this->getBaseCost()
        );

        $state = $this->getState();
        if (is_null($state)) {
            $this->setState(self::STATE_OPEN);
        }
        return $this;
    }

    /**
     * Retrieve Creditmemo states array
     *
     * @return array
     */
    public static function getStates()
    {
        if (is_null(self::$_states)) {
            self::$_states = array(
                self::STATE_OPEN       => Mage::helper('sales')->__('Pending'),
                self::STATE_REFUNDED   => Mage::helper('sales')->__('Refunded'),
                self::STATE_CANCELED   => Mage::helper('sales')->__('Canceled'),
            );
        }
        return self::$_states;
    }

    /**
     * Retrieve Creditmemo state name by state identifier
     *
     * @param   int $stateId
     * @return  string
     */
    public function getStateName($stateId = null)
    {
        if (is_null($stateId)) {
            $stateId = $this->getState();
        }

        if (is_null(self::$_states)) {
            self::getStates();
        }
        if (isset(self::$_states[$stateId])) {
            return self::$_states[$stateId];
        }
        return Mage::helper('sales')->__('Unknown State');
    }

    public function setShippingAmount($amount)
    {
        // base shipping amount calculated in total model
//        $amount = $this->getStore()->roundPrice($amount);
//        $this->setData('base_shipping_amount', $amount);
//
//        $amount = $this->getStore()->roundPrice(
//            $amount*$this->getOrder()->getStoreToOrderRate()
//        );
        $this->setData('shipping_amount', $amount);
        return $this;
    }


    public function setAdjustmentPositive($amount)
    {
        $amount = trim($amount);
        if (substr($amount, -1) == '%') {
            $amount = (float) substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->getStore()->roundPrice($amount);
        $this->setData('base_adjustment_positive', $amount);

        $amount = $this->getStore()->roundPrice(
            $amount*$this->getOrder()->getStoreToOrderRate()
        );
        $this->setData('adjustment_positive', $amount);
        return $this;
    }

    public function setAdjustmentNegative($amount)
    {
        $amount = trim($amount);
        if (substr($amount, -1) == '%') {
            $amount = (float) substr($amount, 0, -1);
            $amount = $this->getOrder()->getGrandTotal() * $amount / 100;
        }

        $amount = $this->getStore()->roundPrice($amount);
        $this->setData('base_adjustment_negative', $amount);

        $amount = $this->getStore()->roundPrice(
            $amount*$this->getOrder()->getStoreToOrderRate()
        );
        $this->setData('adjustment_negative', $amount);
        return $this;
    }

    /**
     * Adds comment to credit memo with additional possibility to send it to customer via email
     * and show it in customer account
     *
     * @param bool $notify
     * @param bool $visibleOnFront
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    public function addComment($comment, $notify=false, $visibleOnFront=false)
    {
        if (!($comment instanceof Mage_Sales_Model_Order_Creditmemo_Comment)) {
            $comment = Mage::getModel('sales/order_creditmemo_comment')
                ->setComment($comment)
                ->setIsCustomerNotified($notify)
                ->setIsVisibleOnFront($visibleOnFront);
        }
        $comment->setCreditmemo($this)
            ->setParentId($this->getId())
            ->setStoreId($this->getStoreId());
        if (!$comment->getId()) {
            $this->getCommentsCollection()->addItem($comment);
        }
        return $this;
    }

    public function getCommentsCollection($reload=false)
    {
        if (is_null($this->_comments) || $reload) {
            $this->_comments = Mage::getResourceModel('sales/order_creditmemo_comment_collection')
                ->setCreditmemoFilter($this->getId())
                ->setCreatedAtOrder();
            /**
             * When credit memo created with adding comment, comments collection must be loaded before we added this comment.
             */
            $this->_comments->load();

            if ($this->getId()) {
                foreach ($this->_comments as $comment) {
                    $comment->setCreditmemo($this);
                }
            }
        }
        return $this->_comments;
    }


    /**
     * Sending email with creditmemo data
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendEmail($notifyCustomer=true, $comment='')
    {
        if (!Mage::helper('sales')->canSendNewCreditmemoEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
            'store' => $this->getStoreId()
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();

        $copyTo = $this->_getEmails(self::XML_PATH_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }
        $paymentBlock   = Mage::helper('payment')->getInfoBlock($order->getPayment())
            ->setIsSecureMode(true);
        $paymentBlock->getMethod()->setStore($order->getStore()->getId());

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'       => $order,
                        'creditmemo'  => $this,
                        'comment'     => $comment,
                        'billing'     => $order->getBillingAddress(),
                        'payment_html'=> $paymentBlock->toHtml(),
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    /**
     * Sending email with invoice update information
     *
     * @return Mage_Sales_Model_Order_Invoice
     */
    public function sendUpdateEmail($notifyCustomer=true, $comment='')
    {
        if (!Mage::helper('sales')->canSendCreditmemoCommentEmail($this->getOrder()->getStore()->getId())) {
            return $this;
        }

        $currentDesign = Mage::getDesign()->setAllGetOld(array(
            'package' => Mage::getStoreConfig('design/package/name', $this->getStoreId()),
        ));

        $translate = Mage::getSingleton('core/translate');
        /* @var $translate Mage_Core_Model_Translate */
        $translate->setTranslateInline(false);

        $order  = $this->getOrder();

        $copyTo = $this->_getEmails(self::XML_PATH_UPDATE_EMAIL_COPY_TO);
        $copyMethod = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_COPY_METHOD, $this->getStoreId());

        if (!$notifyCustomer && !$copyTo) {
            return $this;
        }

        $mailTemplate = Mage::getModel('core/email_template');

        if ($order->getCustomerIsGuest()) {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_GUEST_TEMPLATE, $order->getStoreId());
            $customerName = $order->getBillingAddress()->getName();
        } else {
            $template = Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_TEMPLATE, $order->getStoreId());
            $customerName = $order->getCustomerName();
        }

        if ($notifyCustomer) {
            $sendTo[] = array(
                'name'  => $customerName,
                'email' => $order->getCustomerEmail()
            );
            if ($copyTo && $copyMethod == 'bcc') {
                foreach ($copyTo as $email) {
                    $mailTemplate->addBcc($email);
                }
            }

        }

        if ($copyTo && ($copyMethod == 'copy' || !$notifyCustomer)) {
            foreach ($copyTo as $email) {
                $sendTo[] = array(
                    'name'  => null,
                    'email' => $email
                );
            }
        }

        foreach ($sendTo as $recipient) {
            $mailTemplate->setDesignConfig(array('area'=>'frontend', 'store'=>$order->getStoreId()))
                ->sendTransactional(
                    $template,
                    Mage::getStoreConfig(self::XML_PATH_UPDATE_EMAIL_IDENTITY, $order->getStoreId()),
                    $recipient['email'],
                    $recipient['name'],
                    array(
                        'order'  => $order,
                        'billing'=> $order->getBillingAddress(),
                        'creditmemo'=> $this,
                        'comment'=> $comment
                    )
                );
        }

        $translate->setTranslateInline(true);

        Mage::getDesign()->setAllGetOld($currentDesign);

        return $this;
    }

    protected function _getEmails($configPath)
    {
        $data = Mage::getStoreConfig($configPath, $this->getStoreId());
        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    protected function _beforeDelete()
    {
        $this->_protectFromNonAdmin();
        return parent::_beforeDelete();
    }

    /**
     * After save object manipulations
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _afterSave()
    {
        if (null != $this->_items) {
            foreach ($this->_items as $item) {
                $item->save();
            }
        }

        if (null != $this->_comments) {
            foreach($this->_comments as $comment) {
                $comment->save();
            }
        }


        return parent::_afterSave();
    }

    /**
     * Before object save manipulations
     *
     * @return Mage_Sales_Model_Order_Creditmemo
     */
    protected function _beforeSave()
    {
        parent::_beforeSave();

        if (!$this->getOrderId() && $this->getOrder()) {
            $this->setOrderId($this->getOrder()->getId());
            $this->setBillingAddressId($this->getOrder()->getBillingAddress()->getId());
        }

        return $this;
    }
}
