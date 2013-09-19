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
 * Pbridge payment method model
 *
 * @category    Magento
 * @package     Magento_Pbridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
namespace Magento\Pbridge\Model\Payment\Method;

class Pbridge extends \Magento\Payment\Model\Method\AbstractMethod
{
    /**
     * Config path for system default country
     */
    const XML_CONFIG_PATH_DEFAULT_COUNTRY = 'general/country/default';

    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'pbridge';

    /**
     * Payment method instance wrapped by Payment Bridge
     *
     * @var \Magento\Payment\Model\Method\AbstractMethod
     */
    protected $_originalMethodInstance = null;

    /**
     * Code for wrapped payment method
     *
     * @var string
     */
    protected $_originalMethodCode = null;

    /**
     * Pbridge Api object
     *
     * @var \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    protected $_api = null;

    /**
     * List of address fields
     *
     * @var array
     */
    protected $_addressFileds = array(
        'prefix', 'firstname', 'middlename', 'lastname', 'suffix',
        'company', 'city', 'country_id', 'telephone', 'fax', 'postcode',
    );

    /**
     * Pbridge data
     *
     * @var \Magento\Pbridge\Helper\Data
     */
    protected $_pbridgeData = null;

    /**
     * @param \Magento\Pbridge\Helper\Data $pbridgeData
     * @param \Magento\Core\Model\Event\Manager $eventManager
     * @param \Magento\Payment\Helper\Data $paymentData
     * @param \Magento\Core\Model\Store\Config $coreStoreConfig
     * @param array $data
     */
    public function __construct(
        \Magento\Pbridge\Helper\Data $pbridgeData,
        \Magento\Core\Model\Event\Manager $eventManager,
        \Magento\Payment\Helper\Data $paymentData,
        \Magento\Core\Model\Store\Config $coreStoreConfig,
        array $data = array()
    ) {
        $this->_pbridgeData = $pbridgeData;
        parent::__construct($eventManager, $paymentData, $coreStoreConfig, $data);
    }

    /**
     * Initialize and return Pbridge Api object
     *
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge\Api
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = \Mage::getModel('Magento\Pbridge\Model\Payment\Method\Pbridge\Api');
            $this->_api->setMethodInstance($this);
        }
        return $this->_api;
    }

    /**
     * Check whether payment method can be used
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        return false;
    }

    /**
     * Check if dummy payment method is available
     *
     * @param \Magento\Sales\Model\Quote $quote
     * @return boolean
     */
    public function isDummyMethodAvailable($quote = null)
    {
        $storeId = $quote ? $quote->getStoreId() : null;
        $checkResult = new \StdClass;
        $checkResult->isAvailable = (bool)(int)$this->getOriginalMethodInstance()->getConfigData('active', $storeId);
        $this->_eventManager->dispatch('payment_method_is_active', array(
            'result'          => $checkResult,
            'method_instance' => $this->getOriginalMethodInstance(),
            'quote'           => $quote,
        ));
        $usingPbridge = $this->getOriginalMethodInstance()->getConfigData('using_pbridge', $storeId);
        return $checkResult->isAvailable && $this->_pbridgeData->isEnabled($storeId)
            && $usingPbridge;
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return \Magento\Payment\Model\Info
     */
    public function assignData($data)
    {
        $pbridgeData = array();
        if (is_array($data)) {
            if (isset($data['pbridge_data'])) {
                $pbridgeData = $data['pbridge_data'];
                $data['cc_last4'] = $pbridgeData['cc_last4'];
                $data['cc_type'] = $pbridgeData['cc_type'];
                unset($data['pbridge_data']);
            }
        } else {
            $pbridgeData = $data->getData('pbridge_data');
            $data->setData('cc_last4',$pbridgeData['cc_last4']);
            $data->setData('cc_type',$pbridgeData['cc_type']);
            $data->unsetData('pbridge_data');
        }

        parent::assignData($data);
        $this->setPbridgeResponse($pbridgeData);
        \Mage::getSingleton('Magento\Pbridge\Model\Session')->setToken($this->getPbridgeResponse('token'));
        return $this;
    }

    /**
     * Save Payment Bridge response into the Info instance additional data storage
     *
     * @param array $data
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    public function setPbridgeResponse($data)
    {
        $data = array('pbridge_data' => $data);
        if (!($additionalData = unserialize($this->getInfoInstance()->getAdditionalData()))) {
            $additionalData = array();
        }
        $additionalData = array_merge($additionalData, $data);
        $this->getInfoInstance()->setAdditionalData(serialize($additionalData));
        return $this;
    }

    /**
     * Retrieve Payment Bridge response from the Info instance additional data storage
     *
     * @param string $key
     * @return mixed
     */
    public function getPbridgeResponse($key = null)
    {
        $additionalData = unserialize($this->getInfoInstance()->getAdditionalData());
        if (!is_array($additionalData) || !isset($additionalData['pbridge_data'])) {
            return null;
        }
        if ($key !== null) {
            return isset($additionalData['pbridge_data'][$key]) ? $additionalData['pbridge_data'][$key] : null;
        }
        return $additionalData['pbridge_data'];
    }

    /**
     * Setter
     *
     * @param \Magento\Payment\Model\Method\AbstractMethod $methodInstance
     * @return \Magento\Pbridge\Model\Payment\Method\Pbridge
     */
    public function setOriginalMethodInstance($methodInstance)
    {
        $this->_originalMethodInstance = $methodInstance;
        return $this;
    }

    /**
     * Getter.
     * Retrieve the wrapped payment method instance
     *
     * @return \Magento\Payment\Model\Method\AbstractMethod
     */
    public function getOriginalMethodInstance()
    {
        if (null === $this->_originalMethodInstance) {
            $this->_originalMethodCode = $this->getPbridgeResponse('original_payment_method');
            if (null === $this->_originalMethodCode) {
                return null;
            }
            $this->_originalMethodInstance = $this->_paymentData
                 ->getMethodInstance($this->_originalMethodCode);
        }
        return $this->_originalMethodInstance;
    }

    /**
     * Retrieve payment iformation model object
     *
     * @return \Magento\Payment\Model\Info
     */
    public function getInfoInstance()
    {
        return $this->getOriginalMethodInstance()->getInfoInstance();
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @param string $country
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->getOriginalMethodInstance()->canUseForCountry($country);
    }

    public function validate()
    {
        parent::validate();
        if (!$this->getPbridgeResponse('token')) {
            \Mage::throwException(__("We can't find the Payment Bridge authentication data."));
        }
        return $this;
    }

    /**
     * Authorize
     *
     * @param   \Magento\Object $payment
     * @param   float $amount
     * @return  Magento_Payment_Model_Abstract
     */
    public function authorize(\Magento\Object $payment, $amount)
    {
//        parent::authorize($payment, $amount);
        $order = $payment->getOrder();
        $request = $this->_getApiRequest();

        $request
            ->setData('magento_payment_action' , $this->getOriginalMethodInstance()->getConfigPaymentAction())
            ->setData('client_ip', \Mage::app()->getRequest()->getClientIp(false))
            ->setData('amount', (string)$amount)
            ->setData('currency_code', $order->getBaseCurrencyCode())
            ->setData('order_id', $order->getIncrementId())
            ->setData('customer_email', $order->getCustomerEmail())
            ->setData('is_virtual', $order->getIsVirtual())
            ->setData('notify_url',
                \Mage::getUrl('magento_pbridge/PbridgeIpn/', array('_store' =>  $order->getStore()->getStoreId())))
        ;

        $request->setData('billing_address', $this->_getAddressInfo($order->getBillingAddress()));
        if ($order->getCustomer() && $order->getCustomer()->getId()) {
            $email = $order->getCustomerEmail();
            $id = $order->getCustomer()->getId();
            $request->setData('customer_id',
                $this->_pbridgeData->getCustomerIdentifierByEmail($id, $order->getStore()->getId())
            );
        }

        if (!$order->getIsVirtual()) {
            $request->setData('shipping_address', $this->_getAddressInfo($order->getShippingAddress()));
        }

        $request->setData('cart', $this->_getCart($order));

        $api = $this->_getApi()->doAuthorize($request);
        $apiResponse = $api->getResponse();

        $this->_importResultToPayment($payment, $apiResponse);

        if (isset($apiResponse['fraud']) && (bool)$apiResponse['fraud']) {
            $message = __('Merchant review is required for further processing.');
            $payment->getOrder()->setState(
                  \Magento\Sales\Model\Order::STATE_PROCESSING,
                  \Magento\Sales\Model\Order::STATUS_FRAUD,
                  $message
            );
        }
        return $apiResponse;
    }

    /**
     * Cancel payment
     *
     * @param   \Magento\Object $payment
     * @return  Magento_Payment_Model_Abstract
     */
    public function cancel(\Magento\Object $payment)
    {
        parent::cancel($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   \Magento\Object $payment
     * @param   float $amount
     * @return  Magento_Payment_Model_Abstract
     */
    public function capture(\Magento\Object $payment, $amount)
    {
        //parent::capture($payment, $amount);

        $authTransactionId = $payment->getParentTransactionId();

        if (!$authTransactionId) {
            return false;//$this->authorize($payment, $amount);
        }

        $request = $this->_getApiRequest();
        $request
            ->setData('transaction_id', $authTransactionId)
            ->setData('is_capture_complete', (int)$payment->getShouldCloseParentTransaction())
            ->setData('amount', $amount)
            ->setData('currency_code', $payment->getOrder()->getBaseCurrencyCode())
            ->setData('order_id', $payment->getOrder()->getIncrementId())
        ;

        $api = $this->_getApi()->doCapture($request);
        $this->_importResultToPayment($payment, $api->getResponse());
        $apiResponse = $api->getResponse();

        if (isset($apiResponse['fraud']) && (bool)$apiResponse['fraud']) {
            $message = __('Merchant review is required for further processing.');
            $payment->getOrder()->setState(
                  \Magento\Sales\Model\Order::STATE_PROCESSING,
                  \Magento\Sales\Model\Order::STATUS_FRAUD,
                  $message
            );
        }
        return $apiResponse;
    }

    /**
     * Refund money
     *
     * @param   \Magento\Object $payment
     * @param   float $amount
     * @return  Magento_Payment_Model_Abstract
     */
    public function refund(\Magento\Object $payment, $amount)
    {
        //parent::refund($payment, $amount);

        $captureTxnId = $payment->getParentTransactionId();
        if ($captureTxnId) {
            $order = $payment->getOrder();

            $request = $this->_getApiRequest();
            $request
                ->setData('transaction_id', $captureTxnId)
                ->setData('amount', $amount)
                ->setData('currency_code', $order->getBaseCurrencyCode())
                ->setData('cc_number', $payment->getCcLast4())
            ;

            $canRefundMore = $order->canCreditmemo();
            $allRefunds = (float)$amount
                + (float)$order->getBaseTotalOnlineRefunded()
                + (float)$order->getBaseTotalOfflineRefunded();
            $isFullRefund = !$canRefundMore && (0.0001 > (float)$order->getBaseGrandTotal() - $allRefunds);
            $request->setData('is_full_refund', (int)$isFullRefund);

            // whether to close capture transaction
            $invoiceCanRefundMore = $payment->getCreditmemo()->getInvoice()->canRefund();
            $payment->setShouldCloseParentTransaction($invoiceCanRefundMore ? 0 : 1);
            $payment->setIsTransactionClosed(1);

            $api = $this->_getApi()->doRefund($request);
            $this->_importResultToPayment($payment, $api->getResponse());

            return $api->getResponse();

        } else {
            \Mage::throwException(__("We can't issue a refund transaction because the capture transaction does not exist. "));
        }
    }

    /**
     * Void payment
     *
     * @param   \Magento\Object $payment
     * @return  Magento_Payment_Model_Abstract
     */
    public function void(\Magento\Object $payment)
    {
        //parent::void($payment);

        if ($authTransactionId = $payment->getParentTransactionId()) {
            $request = $this->_getApiRequest();
            $request
                ->setData('transaction_id', $authTransactionId);

            $this->_getApi()->doVoid($request);

        } else {
            \Mage::throwException(__('You need an authorization transaction to void.'));
        }
        return $this->_getApi()->getResponse();
    }

    /**
     * Create address request data
     *
     * @param \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    protected function _getAddressInfo($address)
    {
        $result = array();

        foreach ($this->_addressFileds as $addressField) {
            if ($address->hasData($addressField)) {
                $result[$addressField] = $address->getData($addressField);
            }
        }
        //Streets must be transfered separately
        $streets = $address->getStreet();
        $result['street'] = array_shift($streets) ;
        if ($street2 = array_shift($streets)) {
            $result['street2'] = $street2;
        }
        //Region code lookup
        $region = \Mage::getModel('Magento\Directory\Model\Region')->load($address->getData('region_id'));
        if ($region && $region->getId()) {
            $result['region'] = $region->getCode();
        } else {
            $result['region'] = $address->getRegion();
        }

        return $result;
    }

    /**
     * Public wrapper for _getAddressInfo
     * @param  \Magento\Sales\Model\Order\Address $address
     * @return array
     */
    public function getAddressInfo($address)
    {
        return $this->_getAddressInfo($address);
    }

    /**
     * Fill cart request section from order
     *
     * @param \Magento\Core\Model\AbstractModel $order
     *
     * @return array
     */
    protected function _getCart(\Magento\Core\Model\AbstractModel $order)
    {
        list($items, $totals) = $this->_pbridgeData->prepareCart($order);
        //Getting cart items
        $result = array();

        foreach ($items as $item) {
            $result['items'][] = $item->getData();
        }

        return array_merge($result, $totals);
    }

    /**
     * Transfer API results to payment.
     * Api response must be compatible with payment response expectation
     *
     * @param \Magento\Sales\Model\Order\Payment $payment
     * @param array $apiResponse
     */
    protected function _importResultToPayment(\Magento\Sales\Model\Order\Payment $payment, $apiResponse)
    {
        if (!empty($apiResponse['gateway_transaction_id'])) {
            $payment->setPreparedMessage(
                __('Original gateway transaction id: #%1.', $apiResponse['gateway_transaction_id'])
            );
        }

        if (isset($apiResponse['transaction_id'])) {
            $payment->setTransactionId($apiResponse['transaction_id']);
            unset($apiResponse['transaction_id']);
        }
    }

    /**
     * Return Api request object
     *
     * @return \Magento\Object
     */
    protected function _getApiRequest()
    {
        $request = new \Magento\Object();
        $request->setCountryCode($this->_coreStoreConfig->getConfig(self::XML_CONFIG_PATH_DEFAULT_COUNTRY));
        $request->setClientIdentifier($this->_getCustomerIdentifier());

        return $request;
    }

    /**
     * Return order id
     *
     * @return string
     */
    protected function _getOrderId()
    {
        $orderId = null;
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof \Magento\Sales\Model\Order\Payment) {
            $orderId = $paymentInfo->getOrder()->getIncrementId();
        } else {
            if (!$paymentInfo->getQuote()->getReservedOrderId()) {
                $paymentInfo->getQuote()->reserveOrderId()->save();
            }
            $orderId = $paymentInfo->getQuote()->getReservedOrderId();
        }
        return $orderId;
    }

    /**
     * Return customer identifier
     *
     * @return string
     */
    protected function _getCustomerIdentifier()
    {
        return md5($this->getInfoInstance()->getOrder()->getQuoteId());
    }
}
