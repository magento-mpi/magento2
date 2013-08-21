<?php
/**
 * {license_notice}
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */


/**
 * Ogone Direct Link dummy payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Ogone extends Magento_Payment_Model_Method_Cc
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code = 'pbridge_ogone_direct';

    /**
     * Availability options
     */
    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc = false;
    protected $_canFetchTransactionInfo = true;
    protected $_canReviewPayment        = true;

    /**
     * Form block type for the frontend
     * @var string
     */
    protected $_formBlockType = 'Enterprise_Pbridge_Block_Checkout_Payment_Ogone';

    /**
     * Form block type for the backend
     * @var string
     */
    protected $_backendFormBlockType = 'Enterprise_Pbridge_Block_Adminhtml_Sales_Order_Create_Ogone';

    /**
     * Payment Bridge Payment Method Instance
     * @var Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Return that current payment method is dummy
     * @return boolean
     */
    public function getIsDummy()
    {
        return true;
    }

    /**
     * Check whether payment method can be used
     *
     * @param Magento_Sales_Model_Quote|null $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return Mage::helper('Enterprise_Pbridge_Helper_Data')->isEnabled($quote ? $quote->getStoreId() : null)
            && Magento_Payment_Model_Method_Abstract::isAvailable($quote);
    }

    /**
     * Getter for original method code
     * @return string
     */
    public function getOriginalCode()
    {
        return $this->getCode();
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return Mage::app()->getStore()->isAdmin() ?
            $this->_backendFormBlockType :
            $this->_formBlockType;
    }

    /**
     * Return Payment Bridge method instance
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = Mage::helper('Magento_Payment_Helper_Data')->getMethodInstance('pbridge');
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return Magento_Payment_Model_Info
     */
    public function assignData($data)
    {
        $this->getPbridgeMethodInstance()->assignData($data);
        return $this;
    }

    /**
     * Validate payment method information object
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function authorize(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function capture(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @param float $amount
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function refund(Magento_Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setShouldCloseParentTransaction(false);
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param Magento_Object $payment
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function void(Magento_Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Cancel payment
     *
     * @param Magento_Object $payment
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function cancel(Magento_Object $payment)
    {
        if (!$payment->getOrder()->getInvoiceCollection()->count()) {
            $response = $this->getPbridgeMethodInstance()->void($payment);
            $payment->addData((array)$response);
        }
        return $this;
    }

    /**
     * Getter for Centinel validation availability
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        return false;
    }

    /**
     * Store id setter, also set storeId to helper
     * @param int $store
     * @return Enterprise_Pbridge_Model_Payment_Method_Ogone
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        Mage::helper('Enterprise_Pbridge_Helper_Data')->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }
}
