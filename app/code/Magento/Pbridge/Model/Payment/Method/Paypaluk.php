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
 * Paypal UK Direct dummy payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model\Payment\Method;

class Paypaluk extends \Magento\PaypalUk\Model\Direct
{
    /**
     * Form block type for the frontend
     *
     * @var string
     */
    protected $_formBlockType = 'Magento\Pbridge\Block\Checkout\Payment\Paypaluk';

    /**
     * Form block type for the backend
     *
     * @var string
     */
    protected $_backendFormBlockType = 'Magento\Pbridge\Block\Adminhtml\Sales\Order\Create\Paypal';

    /**
     * Payment Bridge Payment Method Instance
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    protected $_pbridgeMethodInstance = null;

    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\Pbridge\Model\Payment\Method\Paypaluk\Pro';

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * Constructor
     *
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Core\Model\ModuleListInterface $moduleList
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param array $data
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Core\Model\ModuleListInterface $moduleList,
        \Magento\Payment\Helper\Data $paymentData,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($eventManager, $moduleList, $paymentData, $coreStoreConfig, $data);
        $this->_pro->setPaymentMethod($this);
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
            $this->_pbridgeMethodInstance->setOriginalMethodInstance($this);
        }
        return $this->_pbridgeMethodInstance;
    }

    /**
     * Retrieve dummy payment method code
     *
     * @return string
     */
    public function getCode()
    {
        return 'pbridge_' . parent::getCode();
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
     * Retrieve payment method title
     *
     * @return string
     */
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
     * Retrieve information from original payment configuration
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
        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $this->_pro->getConfig()->isMethodAvailable(\Magento\Paypal\Model\Config::METHOD_WPP_PE_DIRECT);
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
     * Prepare info instance for save
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Paypal
     */
    public function prepareSave()
    {
        return $this;
    }

    /**
     * Validate payment method information object
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Paypal
     */
    public function validate()
    {
        $this->getPbridgeMethodInstance()->validate();
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Paypal\Model\Direct
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $result = new \Magento\Object($this->getPbridgeMethodInstance()->authorize($payment, $amount));
        $order = $payment->getOrder();
        $result->setEmail($order->getCustomerEmail());
        $this->_importResultToPayment($result, $payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Paypal\Model\Direct
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        if (false === $this->_pro->capture($payment, $amount)) {
            $this->authorize($payment, $amount);
        }
        return $this;
    }

    /**
     * Refund capture
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Paypal\Model\Direct
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Void payment
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @return \Magento\Paypal\Model\Direct
     */
    public function void(\Magento\Object $payment)
    {
        $this->_pro->void($payment);
        return $this;
    }

    /**
     * Import direct payment results to payment
     *
     * @param \Magento\Object $api
     * @param \Magento\Sales\Model\Order\Payment $payment
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId($api->getTransactionId())->setIsTransactionClosed(0)
            ->setIsTransactionPending($api->getIsPaymentPending());
        $payflowTrxid = $api->getData(\Magento\Pbridge\Model\Payment\Method\Paypaluk\Pro::TRANSPORT_PAYFLOW_TXN_ID);
        $payment->setPreparedMessage(
            __('Payflow PNREF: #%1.', $payflowTrxid)
        );

        $this->_pro->importPaymentInfo($api, $payment);
    }

    /**
     * Disable magento centinel validation for pbridge payment methods
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
     * @param int|string|\Magento\Core\Model\Store $store
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Paypaluk
     */
    public function setStore($store)
    {
        $this->setData('store', $store);
        $this->_pbridgeData->setStoreId(is_object($store) ? $store->getId() : $store);
        parent::setStore($store);

        return $this;
    }
}
