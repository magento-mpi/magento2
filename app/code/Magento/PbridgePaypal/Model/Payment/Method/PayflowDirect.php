<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

use Magento\Paypal\Model\Direct;
use Magento\Sales\Model\Order\Payment;

/**
 * Payflow Direct dummy payment method model
 */
class PayflowDirect extends \Magento\Paypal\Model\PayflowDirect
{
    /**
     * Website Payments Pro instance type
     *
     * @var $_proType string
     */
    protected $_proType = 'Magento\PbridgePaypal\Model\Payment\Method\Payflow\Pro';

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData;

    /**
     * @var Paypal
     */
    protected $_paypal;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Centinel\Model\Service $centinelService
     * @param \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\UrlInterface $urlBuilder
     * @param \Magento\Framework\App\RequestInterface $requestHttp
     * @param \Magento\Paypal\Model\CartFactory $cartFactory
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param Paypal $paypal
     * @param string $formBlock
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        \Magento\Framework\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Framework\Logger $logger,
        \Magento\Framework\Module\ModuleListInterface $moduleList,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\Centinel\Model\Service $centinelService,
        \Magento\Paypal\Model\Method\ProTypeFactory $proTypeFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\UrlInterface $urlBuilder,
        \Magento\Framework\App\RequestInterface $requestHttp,
        \Magento\Paypal\Model\CartFactory $cartFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        Paypal $paypal,
        $formBlock,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        $this->_formBlockType = $formBlock;
        $this->_paypal = $paypal;
        parent::__construct(
            $eventManager,
            $paymentData,
            $scopeConfig,
            $logAdapterFactory,
            $logger,
            $moduleList,
            $localeDate,
            $centinelService,
            $proTypeFactory,
            $storeManager,
            $urlBuilder,
            $requestHttp,
            $cartFactory,
            $data
        );
        $this->_pro->setPaymentMethod($paypal);
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        return $this->_paypal->getPbridgeMethodInstance()->isDummyMethodAvailable(
            $quote
        ) && $this->_pro->getConfig()->isMethodAvailable(
            \Magento\Paypal\Model\Config::METHOD_WPP_PE_DIRECT
        );
    }

    /**
     * Prepare info instance for save
     *
     * @return $this
     */
    public function prepareSave()
    {
        return $this;
    }

    /**
     * Authorize payment
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $payment->setCart($this->_pbridgeData->prepareCart($payment->getOrder()));
        $result = new \Magento\Object($this->_paypal->getPbridgeMethodInstance()->authorize($payment, $amount));
        $order = $payment->getOrder();
        $result->setEmail($order->getCustomerEmail());
        $this->_importResultToPayment($result, $payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
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
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $this->_pro->refund($payment, $amount);
        return $this;
    }

    /**
     * Void payment
     *
     * @param \Magento\Object $payment
     * @return $this
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
     * @param Payment $payment
     * @return void
     */
    protected function _importResultToPayment($api, $payment)
    {
        $payment->setTransactionId(
            $api->getTransactionId()
        )->setIsTransactionClosed(
            0
        )->setIsTransactionPending(
            $api->getIsPaymentPending()
        );
        $payflowTrxid = $api->getData(
            \Magento\PbridgePaypal\Model\Payment\Method\Payflow\Pro::TRANSPORT_PAYFLOW_TXN_ID
        );
        $payment->setPreparedMessage(__('Payflow PNREF: #%1.', $payflowTrxid));

        $this->_pro->importPaymentInfo($api, $payment);
    }
}
