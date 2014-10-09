<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */
namespace Magento\PbridgePaypal\Model\Payment\Method;

/**
 * Payflow Pro dummy payment method model
 */
class Payflowpro extends \Magento\Paypal\Model\Payflowpro
{
    /**
     * @var bool
     */
    protected $_canFetchTransactionInfo = false;

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
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     * @param \Magento\Framework\Math\Random $mathRandom
     * @param \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory
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
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Paypal\Model\ConfigFactory $configFactory,
        \Magento\Framework\Math\Random $mathRandom,
        \Magento\Framework\HTTP\ZendClientFactory $httpClientFactory,
        \Magento\Pbridge\Helper\Data $pbridgeData,
        Paypal $paypal,
        $formBlock = '',
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
            $storeManager,
            $configFactory,
            $mathRandom,
            $httpClientFactory,
            $data
        );
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isAvailable($quote = null)
    {
        $storeId = $this->_storeManager->getStore($this->getStore())->getId();
        $config = $this->_configFactory->create()->setStoreId($storeId);

        return $this->_paypal->getPbridgeMethodInstance()->isDummyMethodAvailable(
            $quote
        ) && $config->isMethodAvailable(
            $this->_paypal->getOriginalCode()
        );
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Framework\Object $payment, $amount)
    {
        $payment->setCart($this->_pbridgeData->prepareCart($payment->getOrder()));
        $response = $this->_paypal->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        if (!empty($response['respmsg'])) {
            $preparedMessage = (string)$payment->getPreparedMessage();
            $preparedMessage .= ' ' . $response['respmsg'];
            if (!empty($response['postfpsmsg'])) {
                $preparedMessage .= ': ' . $response['postfpsmsg'];
            }
            $preparedMessage .= '.';
            $payment->setPreparedMessage($preparedMessage);
        }
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Framework\Object $payment, $amount)
    {
        $payment->setShouldCloseParentTransaction(!$this->_getCaptureAmount($amount));
        $payment->setFirstCaptureFlag(!$this->getInfoInstance()->hasAmountPaid());
        $response = $this->_paypal->getPbridgeMethodInstance()->capture($payment, $amount);
        if (!$response) {
            $payment->setCart($this->_pbridgeData->prepareCart($payment->getOrder()));
            $response = $this->_paypal->getPbridgeMethodInstance()->authorize($payment, $amount);
        }
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Refunding method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Framework\Object $payment, $amount)
    {
        $response = $this->_paypal->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setShouldCloseParentTransaction(!$payment->getCreditmemo()->getInvoice()->canRefund());
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Framework\Object $payment
     * @return $this
     */
    public function void(\Magento\Framework\Object $payment)
    {
        $response = $this->_paypal->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }
}
