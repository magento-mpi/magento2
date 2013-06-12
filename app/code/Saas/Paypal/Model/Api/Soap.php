<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/**
 * SOAP Paypal API wrapper
 *
 * @deprecated Use NVP implementation to work with permissions
 *
 * @category   Saas
 * @package    Saas_Paypal
 * @author     Magento Saas Team <core@magentocommerce.com>
 */
class Saas_Paypal_Model_Api_Soap extends Mage_Paypal_Model_Api_Abstract
{
    /**
     * Setting for ini-setting 'default_socket_timeout'.
     * Needed to increase SOAPClient time to get info (default value is too small: 60 seconds).
     *
     * @var int
     */
    const SOCKET_TIMEOUT = 600;

    /**
     * WSDL Soap Version.
     * Attention: need to update this parameter in case when in PayPal there is WSDL with new version.
     *
     * @var string
     */
    protected $_version = '65.1';

    /**
     * SOAP Client variable.
     *
     * @var Zend_Soap_Client | null
     */
    protected $_soap;

    /**
     * Soap call errors
     *
     * @var array
     */
    protected $_callErrors;

    /**
     * Returns URL to WSDL file
     *
     * @return string
     */
    public function getWsdl()
    {
        return Mage::getModuleDir('etc', 'Saas_Paypal')  . DS . 'wsdl' . DS . 'PayPalSvc.wsdl';
    }

    /**
     * SOAP version getter
     *
     * @return string
     */
    public function getVersion()
    {
        return $this->_version;
    }

    /**
     * Return Paypal Api token based on config data
     *
     * @return string
     */
    public function getBoardingToken()
    {
        return $this->_getDataOrConfig('boarding_token');
    }

    /**
     * Return Paypal Api boarding acount email based on config data
     *
     * @return string
     */
    public function getBoardingAccount()
    {
        return $this->_getDataOrConfig('boarding_account');
    }

    /**
     * Return Paypal Api Program Code based on config data
     *
     * @return string
     */
    public function getProgramCode()
    {
        return $this->_config->programCode;
    }

    /**
     * Get SOAP Client object (also initializes it if needed)
     *
     * @return SoapClient
     */
    public function getSoap()
    {
        if (is_null($this->_soap)) {
            if (ini_get('default_socket_timeout') < self::SOCKET_TIMEOUT) {
                ini_set('default_socket_timeout', self::SOCKET_TIMEOUT);
            }
            $config = array(
                'soap_version' =>SOAP_1_1,
                'cache_wsdl' => WSDL_CACHE_MEMORY,
                'exceptions' => true,
                'trace' => true
            );
            //Fix for PayPal bug with WSDL links in live.
            if (!$this->_config->sandboxFlag) {
                $config['location'] = "https://api-3t.paypal.com/2.0/";
            }
            //
            if ($this->getUseProxy()) {
                $config['proxy_host'] = $this->getProxyHost();
                $config['proxy_port'] = $this->getProxyPort();
            }

            $this->_soap = new SoapClient($this->getWsdl(), $config);
        }
        return $this->_soap;
    }

    /**
     * Initialize PayPal API credentials in SOAP object.
     * Need to call every time because of business_account can be changed.
     */
    protected function getCredentials()
    {
        $credentials = new stdClass();
        $credentials->Username  = $this->getApiUsername();
        $credentials->Password  = $this->getApiPassword();
        $credentials->Signature = $this->getApiSignature();

        $auth = new stdClass();
        $auth->Credentials = $credentials;

        return new SoapHeader('urn:ebay:api:PayPalAPI', 'RequesterCredentials', $auth);
    }

    /**
     * Do the API call
     *
     * @param Saas_Paypal_Model_Api_Soap_Operation_Abstract $operation
     * @throws Exception
     * @return stdClass
     */
    public function call(Saas_Paypal_Model_Api_Soap_Operation_Abstract $operation)
    {
        $request = $operation->getRequest($this);
        $request->Version = $this->getVersion();
        $methodName = $operation->getMethodName();
        $debugData = array($methodName => $operation->toArray($request));

        try {
            $soap = $this->getSoap();
            $response = $soap->__soapCall($methodName, array($request), null, array($this->getCredentials()));
        } catch (Exception $e) {
            $debugData['soap_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        $debugData['response'] = $operation->toArray($response);
        $this->_debug($debugData);

        $this->_callErrors = array();
        if (!$this->_isCallSuccessful($response)) {
            $this->_handleCallErrors($response);
        }

        return $response;
    }

    /**
     * Catch success calls and collect warnings
     *
     * @param stdObject $response
     * @return bool success flag
     */
    protected function _isCallSuccessful($response)
    {
        $ack = strtoupper($response->Ack);
        if ($ack == 'SUCCESS') {
            return true;
        }
        return false;
    }

    /**
     * Handle logical errors
     *
     * @param stdObject $response
     * @throws Mage_Core_Exception
     */
    protected function _handleCallErrors($response)
    {
        if (isset($response->Errors) && isset($response->Errors->ErrorCode)) {
            $error = $response->Errors;
            $longMessage = isset($error->LongMessage)
                ? preg_replace('/\.$/', '', $error->LongMessage) : '';
            $shortMessage = preg_replace('/\.$/', '', $error->ShortMessage);
            $errorInfo = $longMessage
                ? sprintf('%s (#%s: %s).', $longMessage, $error->ErrorCode, $shortMessage)
                : sprintf('#%s: %s.', $error->ErrorCode, $shortMessage);
            $this->_callErrors[] = $error->ErrorCode;

            $e = Mage::exception('Saas_Paypal', Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal NVP gateway errors: %s Correlation ID: %s. Version: %s.',
                $errorInfo,
                isset($response->CorrelationID) ? $response->CorrelationID : '',
                isset($response->Version) ? $response->Version : ''
            ));
            Mage::logException($e);
            $e->setMessage(Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal gateway has rejected request. %s', $errorInfo));
            throw $e;

        }
    }

    /**
     * Log debug data to file
     *
     * @param mixed $debugData
     */
    protected function _debug($debugData)
    {
        if ($this->getDebugFlag()) {
            Mage::getModel('Mage_Core_Model_Log_Adapter', 'paypal_onboarding_requests.log')
               ->setFilterDataKeys($this->_debugReplacePrivateDataKeys)
               ->log($debugData);
        }
    }

    /**
     * EnterBoarding operation
     */
    public function callEnterBoarding()
    {
        /* @var $operation Saas_Paypal_Model_Api_Soap_Operation_EnterBoarding */
        $operation = Mage::getModel('Saas_Paypal_Model_Api_Soap_Operation_EnterBoarding');
        $response = $this->call($operation);
        $operation->setResponse($this, $response);
    }

    /**
     * GetBoardingRequest operation
     */
    public function callGetBoardingDetails()
    {
        /* @var $operation Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails */
        $operation = Mage::getModel('Saas_Paypal_Model_Api_Soap_Operation_GetBoardingDetails');
        $response = $this->call($operation);
        $operation->setResponse($this, $response);
    }
}
