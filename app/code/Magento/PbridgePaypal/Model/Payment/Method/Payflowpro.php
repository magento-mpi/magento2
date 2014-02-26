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
     * @param \Magento\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param \Magento\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Logger $logger
     * @param \Magento\Module\ModuleListInterface $moduleList
     * @param \Magento\Core\Model\LocaleInterface $locale
     * @param \Magento\Centinel\Model\Service $centinelService
     * @param \Magento\Core\Model\StoreManagerInterface $storeManager
     * @param \Magento\Paypal\Model\ConfigFactory $configFactory
     * @param \Magento\Math\Random $mathRandom
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param Paypal $paypal
     * @param string $formBlock
     * @param array $data
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Logger $logger,
        \Magento\Module\ModuleListInterface $moduleList,
        \Magento\Core\Model\LocaleInterface $locale,
        \Magento\Centinel\Model\Service $centinelService,
        \Magento\Core\Model\StoreManagerInterface $storeManager,
        \Magento\Paypal\Model\ConfigFactory $configFactory,
        \Magento\Math\Random $mathRandom,
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
            $coreStoreConfig,
            $logAdapterFactory,
            $logger,
            $moduleList,
            $locale,
            $centinelService,
            $storeManager,
            $configFactory,
            $mathRandom,
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

        return $this->_paypal->getPbridgeMethodInstance()->isDummyMethodAvailable($quote)
            && $config->isMethodAvailable($this->_paypal->getOriginalCode());
    }

    /**
     * Authorization method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $payment->setCart($this->_pbridgeData->prepareCart($payment->getOrder()));
        $response = $this->_paypal->getPbridgeMethodInstance()->authorize($payment, $amount);
        $payment->addData((array)$response);
        $payment->setIsTransactionClosed(0);
        return $this;
    }

    /**
     * Capturing method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function capture(\Magento\Object $payment, $amount)
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
     * @param \Magento\Object $payment
     * @param float $amount
     * @return $this
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $response = $this->_paypal->getPbridgeMethodInstance()->refund($payment, $amount);
        $payment->addData((array)$response);
        $payment->setShouldCloseParentTransaction(!$payment->getCreditmemo()->getInvoice()->canRefund());
        return $this;
    }

    /**
     * Voiding method being executed via Payment Bridge
     *
     * @param \Magento\Object $payment
     * @return $this
     */
    public function void(\Magento\Object $payment)
    {
        $response = $this->_paypal->getPbridgeMethodInstance()->void($payment);
        $payment->addData((array)$response);
        return $this;
    }
}
