<?php
/**
 * {license_notice}
 *
 * @category    Mage
 * @package     Mage_GoogleCheckout
 * @copyright   {copyright}
 * @license     {license_link}
 */

require_once('googlecheckout/googleresponse.php');
require_once('googlecheckout/googlemerchantcalculations.php');
require_once('googlecheckout/googleresult.php');
require_once('googlecheckout/googlerequest.php');

abstract class Mage_GoogleCheckout_Model_Api_Xml_Abstract extends Magento_Object
{
    /**
     * @var Mage_Core_Model_Translate
     */
    protected $_translator;

    /**
     * @param Mage_Core_Model_Translate $translator
     * @param array $data
     */
    public function __construct(Mage_Core_Model_Translate $translator, array $data = array())
    {
        parent::__construct($data);
        $this->_translator = $translator;
    }

    public function log($text, $nl=true)
    {
        error_log(print_r($text, 1) . ($nl ? "\n" : ''), 3, Mage::getBaseDir('log') . DS . 'callback.log');
        return $this;
    }

    public function __()
    {
        $args = func_get_args();
        $expr = new Mage_Core_Model_Translate_Expr(array_shift($args), 'Mage_GoogleCheckout');
        array_unshift($args, $expr);
        return $this->_translator->translate($args);
    }

    public function getMerchantId()
    {
        if (!$this->hasData('merchant_id')) {
            $this->setData('merchant_id', Mage::getStoreConfig('google/checkout/merchant_id', $this->getStoreId()));
        }
        return $this->getData('merchant_id');
    }

    public function getMerchantKey()
    {
        if (!$this->hasData('merchant_key')) {
            $this->setData('merchant_key', Mage::getStoreConfig('google/checkout/merchant_key', $this->getStoreId()));
        }
        return $this->getData('merchant_key');
    }

    public function getServerType()
    {
        if (!$this->hasData('server_type')) {
            $this->setData(
                'server_type',
                Mage::getStoreConfig('google/checkout/sandbox', $this->getStoreId()) ? "sandbox" : ""
            );
        }
        return $this->getData('server_type');
    }

    public function getLocale()
    {
        if (!$this->hasData('locale')) {
            $this->setData('locale', Mage::getStoreConfig('google/checkout/locale', $this->getStoreId()));
        }
        return $this->getData('locale');
    }

    public function getCurrency()
    {
        if (!$this->hasData('currency')) {
            $this->setData('currency', Mage::app()->getStore()->getBaseCurrencyCode());
            //$this->setData('currency', $this->getLocale()=='en_US' ? 'USD' : 'GBP');
        }
        return $this->getData('currency');
    }

    /**
     * Google Checkout Request instance
     *
     * @return GoogleRequest
     */
    public function getGRequest()
    {
        if (!$this->hasData('g_request')) {
            $this->setData('g_request', new GoogleRequest(
                $this->getMerchantId(),
                $this->getMerchantKey(),
                $this->getServerType(),
                $this->getCurrency()
            ));

            //Setup the log file
            $logDir = Mage::getBaseDir('log');
            $this->getData('g_request')->SetLogFiles(
                $logDir . DS . 'googleerror.log',
                $logDir . DS . 'googlemessage.log',
                L_ALL
            );
        }
        return $this->getData('g_request');
    }

    /**
     * Google Checkout Response instance
     *
     * @return GoogleResponse
     */
    public function getGResponse()
    {
        $merchantId = $this->getMerchantId();
        $merchantKey = $this->getMerchantKey();
        if (empty($merchantId) || empty($merchantKey)) {
            Mage::throwException(Mage::helper('Mage_GoogleCheckout_Helper_Data')->__('GoogleCheckout is not configured'));
        }
        if (!$this->hasData('g_response')) {
            $this->setData('g_response', new GoogleResponse(
                $this->getMerchantId(),
                $this->getMerchantKey()
            ));

            //Setup the log file
            $logDir = Mage::getBaseDir('log');
            $this->getData('g_response')->SetLogFiles(
                $logDir . DS . 'googleerror.log',
                $logDir . DS . 'googlemessage.log',
                L_ALL
            );
        }
        return $this->getData('g_response');
    }

    protected function _getBaseApiUrl()
    {
        $url = 'https://';
        if ($this->getServerType()=='sandbox') {
            $url .= 'sandbox.google.com/checkout/api/checkout/v2/';
        } else {
            $url .= 'checkout.google.com/api/checkout/v2/';
        }
        return $url;
    }

    abstract protected function _getApiUrl();

    public function _call($xml)
    {
        $auth = 'Basic ' . base64_encode($this->getMerchantId() . ':' . $this->getMerchantKey());

        $headers = array(
            'Authorization: ' . $auth,
            'Content-Type: application/xml;charset=UTF-8',
            'Accept: application/xml;charset=UTF-8',
        );

        $url = $this->_getApiUrl();
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\r\n" . $xml;

        $debugData = array('request' => $xml, 'dir' => 'out');

        try {
            $http = new Magento_HTTP_Adapter_Curl();
            $http->write('POST', $url, '1.1', $headers, $xml);
            $response = $http->read();
            $response = preg_split('/^\r?$/m', $response, 2);
            $response = trim($response[1]);
            $debugData['result'] = $response;
            $http->close();
        }
        catch (Exception $e) {
            $debugData['result'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->getApi()->debugData($debugData);
            throw $e;
        }

        $this->getApi()->debugData($debugData);
        $result = @simplexml_load_string($response);
        if (!$result) {
            $result = simplexml_load_string(
                '<error><error-message>Invalid response from Google Checkout server</error-message></error>'
            );
        }
        if ($result->getName() == 'error') {
            $this->setError($this->__('Google Checkout: %s', (string)$result->{'error-message'}));
            $this->setWarnings((array)$result->{'warning-messages'});
        } else {
            $this->unsError()->unsWarnings();
        }

        $this->setResult($result);

        return $result;
    }

    protected function _getCallbackUrl()
    {
        return Mage::getUrl(
            'googlecheckout/api',
            array('_forced_secure'=>Mage::getStoreConfig('google/checkout/use_secure_callback_url',$this->getStoreId()))
        );
    }

    /**
     * Recalculate amount to store currency
     *
     * @param float $amount
     * @param Mage_Sales_Model_Quote $quote
     * @return float
     */
    protected function _reCalculateToStoreCurrency($amount, $quote)
    {
        if ($quote->getQuoteCurrencyCode() != $quote->getBaseCurrencyCode()) {
            $amount = $amount * $quote->getStoreToQuoteRate();
            $amount = Mage::app()->getStore()->roundPrice($amount);
        }
        return $amount;
    }

    /**
     * Get Tax Class for Shipping option
     *
     * @param Mage_Sales_Model_Quote $quote
     * @return mixed
     */
    protected function _getTaxClassForShipping($quote)
    {
        return Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_SHIPPING_TAX_CLASS, $quote->getStoreId());
    }
}
