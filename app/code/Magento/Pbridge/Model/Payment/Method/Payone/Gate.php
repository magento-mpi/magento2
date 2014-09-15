<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

namespace Magento\Pbridge\Model\Payment\Method\Payone;

use Magento\Payment\Model\Method\Cc;

class Gate extends \Magento\Pbridge\Model\Payment\Method
{
    /**
     * Payment method code
     *
     * @var string
     */
    protected $_code = 'payone_gate';

    /**
     * @var array
     */
    protected $_allowCurrencyCode = array('EUR');

    /**#@+
     * Availability options
     */
    protected $_canAuthorize = true;
    protected $_canCapture = true;
    protected $_canCapturePartial = false;
    protected $_canRefund = true;
    protected $_canRefundInvoicePartial = false;
    protected $_canVoid = false;
    protected $_canUseInternal = true;
    protected $_canUseCheckout = true;
    protected $_canSaveCc = false;
    /**#@-*/

    /**
     * Url
     *
     * @var \Magento\Framework\UrlInterface
     */
    protected $_url;

    /**
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Logger\AdapterFactory $logAdapterFactory
     * @param \Magento\Framework\Logger $logger
     * @param \Magento\Framework\Module\ModuleListInterface $moduleList
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param \Magento\Centinel\Model\Service $centinelService
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Framework\StoreManagerInterface $storeManager
     * @param \Magento\Framework\UrlInterface $url
     * @param string $formBlock
     * @param array $data
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
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Framework\StoreManagerInterface $storeManager,
        \Magento\Framework\UrlInterface $url,
        $formBlock = '',
        array $data = array()
    ) {
        $this->_url = $url;
        parent::__construct(
            $eventManager,
            $paymentData,
            $scopeConfig,
            $logAdapterFactory,
            $logger,
            $moduleList,
            $localeDate,
            $centinelService,
            $pbridgeData,
            $storeManager,
            $formBlock,
            $data
        );
    }

    /**
     * Do not validate payment form using server methods
     *
     * @return  bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Set order status to Pending until IPN
     *
     * @return bool
     */
    public function isInitializeNeeded()
    {
        if ($this->is3dSecureEnabled()) {
            return true;
        }
        return parent::isInitializeNeeded();
    }

    /**
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param \Magento\Framework\Object $stateObject
     * @return $this
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE:
            case \Magento\Payment\Model\Method\AbstractMethod::ACTION_AUTHORIZE_CAPTURE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

                $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
        return $this;
    }

    /**
     * Whether order created required on Order Review page or not
     *
     * @return bool
     */
    public function getIsPendingOrderRequired()
    {
        if ($this->is3dSecureEnabled()) {
            return true;
        }
        return false;
    }

    /**
     * Return URL after order placed successfully. Redirect parent to checkout/success
     *
     * @return string
     */
    public function getRedirectUrlSuccess()
    {
        return $this->_url->getUrl('magento_pbridge/pbridge/onepagesuccess', array('_secure' => true));
    }

    /**
     * Return URL after order placed with errors. Redirect parent to checkout/failure
     *
     * @return string
     */
    public function getRedirectUrlError()
    {
        return $this->_url->getUrl('magento_pbridge/pbridge/cancel', array('_secure' => true));
    }
}
