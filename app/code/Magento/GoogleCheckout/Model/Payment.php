<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Magento\GoogleCheckout\Model;

class Payment extends \Magento\Payment\Model\Method\AbstractMethod
{
    const ACTION_AUTHORIZE = 0;
    const ACTION_AUTHORIZE_CAPTURE = 1;

    protected $_code  = 'googlecheckout';
    protected $_formBlockType = 'Magento\GoogleCheckout\Block\Form';

    /**
     * Availability options
     */
    protected $_isGateway               = false;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
    protected $_canCapturePartial       = true;
    protected $_canRefund               = true;
    protected $_canRefundInvoicePartial = true;
    protected $_canVoid                 = true;
    protected $_canUseInternal          = false;
    protected $_canUseCheckout          = false;

    /**
     * @var \Magento\GoogleCheckout\Model\ApiFactory
     */
    protected $apiFactory;

    /**
     * @var \Magento\UrlFactory
     */
    protected $urlFactory;

    /**
     * @var \Magento\Stdlib\DateTime\DateTimeFactory
     */
    protected $dateFactory;

    public function __construct(
        \Magento\Event\ManagerInterface $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Store\ConfigInterface $coreStoreConfig,
        \Magento\Logger\AdapterFactory $logAdapterFactory,
        \Magento\Stdlib\DateTime\DateTimeFactory $dateFactory,
        \Magento\UrlFactory $urlFactory,
        \Magento\GoogleCheckout\Model\ApiFactory $apiFactory,
        array $data = array()
    ) {
        $this->dateFactory = $dateFactory;
        $this->urlFactory = $urlFactory;
        $this->apiFactory = $apiFactory;
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $logAdapterFactory, $data);
    }

    /**
     * Can be edit order (renew order)
     *
     * @return bool
     */
    public function canEdit()
    {
        return false;
    }

    /**
     *  Return Order Place Redirect URL
     *
     *  @return string Order Redirect URL
     */
    public function getOrderPlaceRedirectUrl()
    {
        return $this->urlFactory->create()->getUrl('googlecheckout/redirect/redirect');
    }

    /**
     * Authorize
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return  \Magento\GoogleCheckout\Model\Payment
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
        $api = $this->apiFactory->create()->setStoreId($payment->getOrder()->getStoreId());
        $api->authorize($payment->getOrder()->getExtOrderId());

        return $this;
    }

    /**
     * Capture payment
     *
     * @param \Magento\Object $payment
     * @param float $amount
     * @return  \Magento\GoogleCheckout\Model\Payment
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        if ($payment->getOrder()->getPaymentAuthExpiration() < $this->dateFactory->create()->gmtTimestamp()) {
            try {
                $this->authorize($payment, $amount);
            } catch (\Exception $e) {
                // authorization is not expired yet
            }
        }

        $api = $this->apiFactory->create()->setStoreId($payment->getOrder()->getStoreId());
        $api->charge($payment->getOrder()->getExtOrderId(), $amount);
        $payment->setForcedState(\Magento\Sales\Model\Order\Invoice::STATE_OPEN);

        return $this;
    }

    /**
     * Refund money
     *
     * @param \Magento\Object $payment
     * @param float $amount
     *
     * @return  \Magento\GoogleCheckout\Model\Payment
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        $reason = $this->getReason() ? $this->getReason() : __('No Reason');
        $comment = $this->getComment() ? $this->getComment() : __('No Comment');

        $api = $this->apiFactory->create()->setStoreId($payment->getOrder()->getStoreId());
        $api->refund($payment->getOrder()->getExtOrderId(), $amount, $reason, $comment);

        return $this;
    }

    public function void(\Magento\Object $payment)
    {
        $this->cancel($payment);

        return $this;
    }

    /**
     * Void payment
     *
     * @param \Magento\Object $payment
     *
     * @return \Magento\GoogleCheckout\Model\Payment
     */
    public function cancel(\Magento\Object $payment)
    {
        if (!$payment->getOrder()->getBeingCanceledFromGoogleApi()) {
            $reason = $this->getReason() ? $this->getReason() : __('Unknown Reason');
            $comment = $this->getComment() ? $this->getComment() : __('No Comment');

            $api = $this->apiFactory->create()->setStoreId($payment->getOrder()->getStoreId());
            $api->cancel($payment->getOrder()->getExtOrderId(), $reason, $comment);
        }

        return $this;
    }

    /**
     * Retrieve information from payment configuration
     *
     * @param string $field
     * @param int|string|null|\Magento\Core\Model\Store $storeId
     *
     * @return  mixed
     */
    public function getConfigData($field, $storeId = null)
    {
        if (null === $storeId) {
            $storeId = $this->getStore();
        }
        $path = 'google/checkout/' . $field;

        return $this->_coreStoreConfig->getConfig($path, $storeId);
    }

    /**
     * Check void availability
     *
     * @param   \Magento\Object $payment
     * @return  bool
     */
    public function canVoid(\Magento\Object $payment)
    {
        if ($payment instanceof \Magento\Sales\Model\Order\Invoice
            || $payment instanceof \Magento\Sales\Model\Order\Creditmemo
        ) {
            return false;
        }

        return $this->_canVoid;
    }
}
