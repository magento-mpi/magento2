<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Dibs dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Payment\Method;

class Dibs extends \Magento\Payment\Model\Method\Cc
{
    /**
     * Dibs payment method code
     *
     * @var string
     */
    const PAYMENT_CODE = 'dibs';

    protected $_code = self::PAYMENT_CODE;

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = false;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;

    protected $_currenciesNumbers;

    /**
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento\Pbridge\Block\Checkout\Payment\Dibs';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Dibs';

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Pbridge data
     *
     * @var Magento_Pbridge_Helper_Data
     */
    protected $_pbridgeData = null;

    /**
     * @param Magento_Core_Model_Event_Manager $eventManager
     * @param Magento_Pbridge_Helper_Data $pbridgeData
     * @param Magento_Core_Model_ModuleListInterface $moduleList
     * @param Magento_Payment_Helper_Data $paymentData
     * @param array $data
     */
    public function __construct(
        Magento_Core_Model_Event_Manager $eventManager,
        Magento_Pbridge_Helper_Data $pbridgeData,
        Magento_Core_Model_ModuleListInterface $moduleList,
        Magento_Payment_Helper_Data $paymentData,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($eventManager, $moduleList, $paymentData, $data);
    }


    /**
     * Return that current payment method is dummy
     *
     * @return boolean
     */
    public function getIsDummy()
    {
        return true;
    }

    /**
     * Return Payment Bridge method instance
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    public function getPbridgeMethodInstance()
    {
        if ($this->_pbridgeMethodInstance === null) {
            $this->_pbridgeMethodInstance = $this->_paymentData->getMethodInstance('pbridge');
            if ($this->_pbridgeMethodInstance) {
                $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
            }
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Retrieve original payment method code
     *
     * @return string
     */
    public function getOriginalCode()
    {
        return parent::getCode();
    }

    public function getTitle()
    {
        return parent::getTitle();
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return \Magento\Payment\Model\Info
     */
    public function assignData($data)
    {
        $this->getPbridgeMethodInstance()->assignData($data);
        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int $storeId
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'payment/'.$this->getOriginalCode().'/'.$field;
        return \Mage::getStoreConfig($path, $storeId);
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance() ?
            $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote) : false;
    }

    /**
     * Retrieve block type for method form generation
     *
     * @return string
     */
    public function getFormBlockType()
    {
        return \Mage::app()->getStore()->isAdmin() ?
            $this->_backendFormBlockType :
            $this->_formBlockType;
    }

    /**
     * Validate payment method information object
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $response = $this->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $response = $this->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(1);
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function void(\Magento\Object $payment)
    {
        $response = $this->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(1);
        return $this;
    }

    /**
     * Cancel method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @return \Magento\Pbridge\Model\Payment\Method\Authorizenet
     */
    public function cancel(\Magento\Object $payment)
    {
        return $this->void($payment);
    }

    /**
     * Return payment method Centinel validation status
     *
     * @return bool
     */
    public function getIsCentinelValidationEnabled()
    {
        return false;
    }

    /**
     * Store id setter, also set storeId to helper
     *
     * @param int $store
     * @return \Magento\Pbridge\Model\Payment\Method\Dibs
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
    }

    /**
     * Check refund availability
     *
     * @return bool
     */
    public function canRefund()
    {
         return $this->_canRefund;
    }

    /**
     * Set capture transaction ID to invoice for informational purposes
     * @param \Magento\Sales\Model\Order\Invoice $invoice
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function processInvoice($invoice, $payment)
    {
        $invoice->setTransactionId($payment->getLastTransId());
        return $this;
    }
}
