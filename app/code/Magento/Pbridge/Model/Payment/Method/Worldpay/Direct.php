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
 * Worldpay dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento
 */
namespace Magento\Pbridge\Model\Payment\Method\Worldpay;

class Direct extends \Magento\Payment\Model\Method\Cc
{
    /**
     * Payment method code
     * @var string
     */
    protected $_code  = 'worldpay_direct';
    protected $_allowCurrencyCode = array('USD');

    /**
     * Availability options
     */
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

    /**
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento\Pbridge\Block\Checkout\Payment\Worldpay\Direct';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Worldpay\Direct';

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

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        if(!$this->getPbridgeMethodInstance() || !$this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)) {
            return false;
        }

        return true;
    }
    /**
     * Retrieve information from payment configuration
     *
     * @param   string $field
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
     * Validate payment method information object
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * PSi Gate method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
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
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
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
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
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
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
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
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
     */
    public function cancel(\Magento\Object $payment)
    {
        return $this->void($payment);
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
     * Store id setter, also set storeId to helper
     * @param int $store
     * @return \Magento\Pbridge\Model\Payment\Method\Worldpay\Direct
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        return $this;
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
