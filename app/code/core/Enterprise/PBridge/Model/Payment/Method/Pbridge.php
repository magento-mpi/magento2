<?php
/**
 * Magento Enterprise Edition
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Magento Enterprise Edition License
 * that is bundled with this package in the file LICENSE_EE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.magentocommerce.com/license/enterprise-edition
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Magento to newer
 * versions in the future. If you wish to customize Magento for your
 * needs please refer to http://www.magentocommerce.com for more information.
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @copyright   Copyright (c) 2009 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license     http://www.magentocommerce.com/license/enterprise-edition
 */


/**
 * Pbridge payment method model
 *
 * @category    Enterprise
 * @package     Enterprise_PBridge
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Enterprise_Pbridge_Model_Payment_Method_Pbridge extends Mage_Payment_Model_Method_Abstract
{
    /**
     * Payment code name
     *
     * @var string
     */
    protected $_code = 'pbridge';

    /**
     * Payment method instance wrapped by Payment Bridge
     *
     * @var Mage_Payment_Model_Method_Abstract
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
     * @var Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    protected $_api = null;

    /**
     * List of address fields
     *
     * @var unknown_type
     */
    protected $_addressFileds = array(
        'prefix', 'firstname', 'middlename', 'lastname', 'suffix',
        'company', 'city', 'country_id', 'telephone', 'fax', 'postcode',
    );

    /**
     * Initialize and return Pbridge Api object
     *
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge_Api
     */
    protected function _getApi()
    {
        if ($this->_api === null) {
            $this->_api = Mage::getModel('enterprise_pbridge/payment_method_pbridge_api');
            $this->_api->setMethodInstance($this);
        }
        return $this->_api;
    }

    /**
     * Check whether payment method can be used
     *
     * @param Mage_Sales_Model_Quote
     * @return bool
     */
    public function isAvailable($quote = null)
    {
        $storeId = $quote ? $quote->getStoreId() : null;
        return parent::isAvailable($quote) && Mage::helper('enterprise_pbridge')->isAvailable($storeId);
    }

    /**
     * Assign data to info model instance
     *
     * @param  mixed $data
     * @return Mage_Payment_Model_Info
     */
    public function assignData($data)
    {
        $pbridgeData = array();
        if (is_array($data)) {
            if (isset($data['pbridge_data'])) {
                $pbridgeData = $data['pbridge_data'];
                unset($data['pbridge_data']);
            }
        } else {
            $pbridgeData = $data->getData('pbridge_data');
            $data->unsetData('pbridge_data');
        }
        parent::assignData($data);
        $this->setInfoAdditionalData($pbridgeData);
        return $this;
    }

    /**
     * Save additional Payment Bridge parameters into the Info instance additional data storage
     *
     * @param array $data
     * @return Enterprise_Pbridge_Model_Payment_Method_Pbridge
     */
    public function setInfoAdditionalData($data)
    {
        if (empty($data)) {
            return $this;
        }
        $additionaData = $this->getInfoInstance()->getAdditionalData();
        if ($additionaData) {
            $additionalData = array_merge(unserialize($additionaData), array('pbridge_data' => $data));
        } else {
            $additionalData = array('pbridge_data' => $data);
        }
        $this->getInfoInstance()->setAdditionalData(serialize($additionalData));
        return $this;
    }

    /**
     * Retrieve additional Payment Bridge parameters from the Info instance additional data storage
     *
     * @param string $param OPTIONAL
     * @return mixed
     */
    public function getInfoAdditionalData($param = null)
    {
        if (!$this->getData('info_instance')) {
            return null;
        }
        $additionaData = $this->getInfoInstance()->getAdditionalData();
        if (!$additionaData) {
            return null;
        }
        $additionaData = unserialize($additionaData);
        if (!isset($additionaData['pbridge_data'])) {
            return null;
        }
        if (null === $param) {
            return $additionaData['pbridge_data'];
        }
        return isset($additionaData['pbridge_data'][$param]) ? $additionaData['pbridge_data'][$param] : null;
    }

    /**
     * Retrieve Payment Bridge token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->getInfoAdditionalData('token');
    }

    public function setOriginalMethodInstance($methodInstance)
    {
        $this->_originalMethodInstance = $methodInstance;
        return $this;
    }

    /**
     * Getter.
     * Retrieve the wrapped payment method instance
     *
     * @return Mage_Payment_Model_Method_Abstract
     */
    public function getOriginalMethodInstance()
    {
        if (null === $this->_originalMethodInstance) {
            $this->_originalMethodCode = $this->getInfoAdditionalData('original_payment_method');
            if (null === $this->_originalMethodCode) {
                return null;
            }
            $this->_originalMethodInstance = Mage::helper('payment')
                 ->getMethodInstance($this->_originalMethodCode);
        }
        return $this->_originalMethodInstance;
    }

    /**
     * Retrieve payment iformation model object
     *
     * @return Mage_Payment_Model_Info
     */
    public function getInfoInstance()
    {
        $instance = $this->getOriginalMethodInstance()->getData('info_instance');
        if (!($instance instanceof Mage_Payment_Model_Info)) {
            Mage::throwException($this->_getHelper()->__('Can not retrieve payment iformation object instance'));
        }
        return $instance;
    }

    /**
     * To check billing country is allowed for the payment method
     *
     * @return bool
     */
    public function canUseForCountry($country)
    {
        return $this->getOriginalMethodInstance()->canUseForCountry($country);
    }

    public function validate()
    {
        parent::validate();
        return $this;
    }

    /**
     * Authorize
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function authorize(Varien_Object $payment, $amount)
    {
//        parent::authorize($payment, $amount);
        $order = $payment->getOrder();
        $request = new Varien_Object();

        $request
            ->setData('magento_payment_action' , $this->getOriginalMethodInstance()->getConfigPaymentAction())
            ->setData('client_ip', Mage::app()->getRequest()->getClientIp(false))
            ->setData('amount', (string)$amount)
            ->setData('currency_code', $order->getBaseCurrencyCode())
            ->setData('order_id', $order->getIncrementId())
            ->setData('customer_email', $order->getCustomerEmail())
            ->setData('is_virtual', $order->getIsVirtual())
            ->setData('notify_url', Mage::getUrl('enterprise_pbridge/PbridgeIpn/'))
        ;

        $request->setData('billing_address', $this->_getAddressInfo($order->getBillingAddress()));
        $request->setData('shipping_address', $this->_getAddressInfo($order->getShippingAddress()));

        $request->setData('cart', $this->_getCart($order));

        $api = $this->_getApi()->doAuthorize($request);
        $apiResponse = $api->getResponse();

        $this->_importResultToPayment($payment, $apiResponse);

        return $apiResponse;

    }

    /**
     * Cancel payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function cancel(Varien_Object $payment)
    {
        parent::cancel($payment);
        return $this;
    }

    /**
     * Capture payment
     *
     * @param   Varien_Object $orderPayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function capture(Varien_Object $payment, $amount)
    {
        //parent::capture($payment, $amount);

        $authTransactionId = $payment->getParentTransactionId();

        if (!$authTransactionId) {
            return false;//$this->authorize($payment, $amount);
        }

        $request = new Varien_Object();
        $request
            ->setData('transaction_id', $authTransactionId)
            ->setData('is_capture_complete', (int)$payment->getShouldCloseParentTransaction())
            ->setData('amount', $amount)
            ->setData('currency_code', $payment->getOrder()->getBaseCurrencyCode())
            ->setData('order_id', $payment->getOrder()->getIncrementId())
        ;

        $api = $this->_getApi()->doCapture($request);
        $this->_importResultToPayment($payment, $api->getResponse());
        return $api->getResponse();
    }

    /**
     * Refund money
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function refund(Varien_Object $payment, $amount)
    {
        //parent::refund($payment, $amount);

        $captureTxnId = $payment->getParentTransactionId();
        if ($captureTxnId) {
            $order = $payment->getOrder();

            $request = new Varien_Object();
            $request
                ->setData('transaction_id', $captureTxnId)
                ->setData('amount', $amount)
                ->setData('currency_code', $order->getBaseCurrencyCode())
            ;

            $canRefundMore = $order->canCreditmemo(); // TODO: fix this to be able to create multiple refunds
            $isFullRefund = !$canRefundMore
                && (0 == ((float)$order->getBaseTotalOnlineRefunded() + (float)$order->getBaseTotalOfflineRefunded()));
            $request->setData('is_full_refund', (int)$isFullRefund);

            $api = $this->_getApi()->doRefund($request);
            $this->_importResultToPayment($payment, $api->getResponse());

            return $api->getResponse();

        } else {
            Mage::throwException(Mage::helper('enterprise_pbridge')->__('Impossible to issue a refund transaction, because capture transaction does not exist.'));
        }
    }

    /**
     * Void payment
     *
     * @param   Varien_Object $invoicePayment
     * @return  Mage_Payment_Model_Abstract
     */
    public function void(Varien_Object $payment)
    {
        //parent::void($payment);

        if ($authTransactionId = $payment->getParentTransactionId()) {
            $request = new Varien_Object();
            $request
                ->setData('transaction_id', $authTransactionId);

            $this->_getApi()->doVoid($request);

        } else {
            Mage::throwException(Mage::helper('enterprise_pbridge')->__('Authorization transaction is required to void.'));
        }
        return $this->_getApi()->getResponse();
    }

    /**
     * Create address request data
     *
     * @param $address
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
        $region = Mage::getModel('directory/region')->load($address->getData('region_id'));
        if ($region && $region->getId()) {
            $result['region'] = $region->getCode();
        }

        return $result;
    }

    /**
     * Fill cart request section from order
     *
     * @param Mage_Core_Model_Abstract $order
     *
     * @return array
     */
    protected function _getCart(Mage_Core_Model_Abstract $order)
    {
        list($items, $totals) = Mage::helper('enterprise_pbridge')->prepareCart($order);
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
     * @param Mage_Sales_Model_Order_Payment $payment
     * @param array $apiResponse
     */
    protected function _importResultToPayment(Mage_Sales_Model_Order_Payment $payment, $apiResponse)
    {
        if (!empty($apiResponse['gateway_transaction_id'])) {
            $payment->setPreparedMessage(Mage::helper('enterprise_pbridge')->__('Original gateway transaction id: #%s.',
                $apiResponse['gateway_transaction_id']));
        }

        if (isset($apiResponse['transaction_id'])) {
            $payment->setTransactionId($apiResponse['transaction_id']);
            unset($apiResponse['transaction_id']);
        }
    }
}
