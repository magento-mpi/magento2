<?php
/**
 * {license_notice}
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * Website Payments Pro Hosted Solution payment gateway model
 *
 * @category    Magento
 * @package     Magento_Paypal
 * @author      Magento Core Team <core@magentocommerce.com>
 */

namespace Magento\Paypal\Model;

class Hostedpro extends \Magento\Paypal\Model\Direct
{
    /**
     * Button code
     *
     * @var string
     */
    const BM_BUTTON_CODE    = 'TOKEN';

    /**
     * Button type
     *
     * @var string
     */
    const BM_BUTTON_TYPE    = 'PAYMENT';

    /**
     * Paypal API method name for button creation
     *
     * @var string
     */
    const BM_BUTTON_METHOD  = 'BMCreateButton';

    /**
     * Payment method code
     */
    protected $_code = \Magento\Paypal\Model\Config::METHOD_HOSTEDPRO;

    protected $_formBlockType = '\Magento\Paypal\Block\Hosted\Pro\Form';
    protected $_infoBlockType = '\Magento\Paypal\Block\Hosted\Pro\Info';

    /**
     * Availability options
     */
    protected $_canUseInternal          = false;
    protected $_canUseForMultishipping  = false;
    protected $_canSaveCc               = false;
    protected $_isInitializeNeeded      = true;

    /**
     * Return available CC types for gateway based on merchant country.
     * We do not have to check the availability of card types.
     *
     * @return bool
     */
    public function getAllowedCcTypes()
    {
        return true;
    }

    /**
     * Return merchant country code from config,
     * use default country if it not specified in General settings
     *
     * @return string
     */
    public function getMerchantCountry()
    {
        return $this->_pro->getConfig()->getMerchantCountry();
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
     * Instantiate state and set it to state object
     *
     * @param string $paymentAction
     * @param \Magento\Object $stateObject
     */
    public function initialize($paymentAction, $stateObject)
    {
        switch ($paymentAction) {
            case \Magento\Paypal\Model\Config::PAYMENT_ACTION_AUTH:
            case \Magento\Paypal\Model\Config::PAYMENT_ACTION_SALE:
                $payment = $this->getInfoInstance();
                $order = $payment->getOrder();
                $order->setCanSendNewEmailFlag(false);
                $payment->setAmountAuthorized($order->getTotalDue());
                $payment->setBaseAmountAuthorized($order->getBaseTotalDue());

                $this->_setPaymentFormUrl($payment);

                $stateObject->setState(\Magento\Sales\Model\Order::STATE_PENDING_PAYMENT);
                $stateObject->setStatus('pending_payment');
                $stateObject->setIsNotified(false);
                break;
            default:
                break;
        }
    }

    /**
     * Sends API request to PayPal to get form URL, then sets this URL to $payment object.
     *
     * @param \Magento\Payment\Model\Info $payment
     */
    protected function _setPaymentFormUrl(\Magento\Payment\Model\Info $payment)
    {
        $request = $this->_buildFormUrlRequest($payment);
        $response = $this->_sendFormUrlRequest($request);
        if ($response) {
            $payment->setAdditionalInformation('secure_form_url', $response);
        } else {
            \Mage::throwException('Cannot get secure form URL from PayPal');
        }
    }

    /**
     * Returns request object with needed data for API request to PayPal to get form URL.
     *
     * @param \Magento\Payment\Model\Info $payment
     * @return \Magento\Paypal\Model\Hostedpro\Request
     */
    protected function _buildFormUrlRequest(\Magento\Payment\Model\Info $payment)
    {
        $request = $this->_buildBasicRequest()
            ->setOrder($payment->getOrder())
            ->setPaymentMethod($this);

        return $request;
    }

    /**
     * Returns form URL from request to PayPal.
     *
     * @param \Magento\Paypal\Model\Hostedpro\Request $request
     * @return string | false
     */
    protected function _sendFormUrlRequest(\Magento\Paypal\Model\Hostedpro\Request $request)
    {
        $api = $this->_pro->getApi();
        $response = $api->call(self::BM_BUTTON_METHOD, $request->getRequestData());

        if (!isset($response['EMAILLINK'])) {
            return false;
        }
        return $response['EMAILLINK'];
    }

    /**
     * Return request object with basic information
     *
     * @return \Magento\Paypal\Model\Hostedpro\Request
     */
    protected function _buildBasicRequest()
    {
        $request = \Mage::getModel('Magento\Paypal\Model\Hostedpro\Request');
        $request->setData(array(
            'METHOD'     => self::BM_BUTTON_METHOD,
            'BUTTONCODE' => self::BM_BUTTON_CODE,
            'BUTTONTYPE' => self::BM_BUTTON_TYPE
        ));
        return $request;
    }

    /**
     * Get return URL
     *
     * @param int $storeId
     * @return string
     */
    public function getReturnUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/return', $storeId);
    }

    /**
     * Get notify (IPN) URL
     *
     * @param int $storeId
     * @return string
     */
    public function getNotifyUrl($storeId = null)
    {
        return $this->_getUrl('paypal/ipn', $storeId, false);
    }

    /**
     * Get cancel URL
     *
     * @param int $storeId
     * @return string
     */
    public function getCancelUrl($storeId = null)
    {
        return $this->_getUrl('paypal/hostedpro/cancel', $storeId);
    }

    /**
     * Build URL for store
     *
     * @param string $path
     * @param int $storeId
     * @param bool $secure
     * @return string
     */
    protected function _getUrl($path, $storeId, $secure = null)
    {
        $store = \Mage::app()->getStore($storeId);
        return \Mage::getUrl($path, array(
            "_store"   => $store,
            "_secure"  => is_null($secure) ? $store->isCurrentlySecure() : $secure
        ));
    }
}
