<?php
/**
 * {license_notice}
 *
 * @category    Saas
 * @package     Saas_Paypal
 * @copyright   {copyright}
 * @license     {license_link}
 */

/*
 * Implemented version 89.0 of API permissions protocol
 */
class Saas_Paypal_Model_Api_Permission_Nvp extends Mage_Paypal_Model_Api_Abstract
{
    /**
     * Permissions API operations
     */
    const CALL_REQUEST_PERMISSIONS = 'RequestPermissions';
    const CALL_GET_ACCESS_TOKEN = 'GetAccessToken';
    const CALL_GET_BASIC_PERSONAL_DATA  = 'GetBasicPersonalData';

    /**
     * Language in which error messages are returned
     */
    const REQUEST_ERROR_LANGUAGE = 'en_US';

    /**
     * Basic personal data
     *
     * @var array
     */
    protected $_basicPersonalData = array(
        'firstName'    => 'http://axschema.org/namePerson/first',
        'lastName'     => 'http://axschema.org/namePerson/last',
        'contactEmail' => 'http://axschema.org/contact/email',
        'fullName'     => 'http://schema.openid.net/contact/fullname',
        'companyName'  => 'http://axschema.org/company/name',
        'countryCode'  => 'http://axschema.org/contact/country/home',
        'payerId'      => 'https://www.paypal.com/webapps/auth/schema/payerID'
    );

    /**
     * Do RequestPermissions API call and return token
     *
     * @param array $groups
     * @return string
     */
    public function requestPermissions($groups)
    {
        $request = array();
        foreach ($groups as $group) {
            $request['scope'][] = $group;
        }

        $request['callback'] = Mage::helper('Saas_Paypal_Helper_Data')->getRedirectUrl();
        $response = $this->call(self::CALL_REQUEST_PERMISSIONS, $this->_prepareRequest($request));

        return $response['token'];
    }

    /**
     * Do GetAccessToken API call and return token and secret key
     *
     * @param string $token
     * @param string $verifier
     * @return array
     */
    public function getAccessData($token, $verifier)
    {
        $request = array(
            'token' => $token,
            'verifier' => $verifier
        );

        $response = $this->call(self::CALL_GET_ACCESS_TOKEN, $this->_prepareRequest($request));
        return $response;
    }

    /**
     * Do GetBasicPersonalData API call
     *
     * @param string $token
     * @param string $code
     * @return array
     */
    public function getBasicPersonalData($token, $code)
    {
        $timestamp = time();
        $params = array(
            'oauth_consumer_key'     => $this->getApiUsername(),
            'oauth_signature_method' => 'HMAC-SHA1',
            'oauth_timestamp'        => $timestamp,
            'oauth_token'            => $token,
            'oauth_version'          => '1.0',
        );

        $signature = $this->_prepareSignature(
            $params,
            $this->getApiPassword(),
            $code,
            $this->getApiEndpoint(self::CALL_GET_BASIC_PERSONAL_DATA)
        );

        $headers = array(
            'X-PAYPAL-AUTHORIZATION: ' . sprintf('timestamp=%s,token=%s,signature=%s', $timestamp, $token, $signature)
        );

        $position = 0;
        foreach ($this->_basicPersonalData as $schema) {
            $request['attributeList.attribute(' . $position . ')'] = $schema;
            $position++;
        }

        $response = $this->call(self::CALL_GET_BASIC_PERSONAL_DATA, $this->_prepareRequest($request), $headers);
        return $response;
    }

    /**
     * Prepare encoded string
     *
     * @param array $parts
     * @return string
     */
    protected function _prepareString($parts)
    {
        foreach ($parts as $key => $value) {
            $search  = array('%7E', '.', '*', '+', '-');
            $replace = array('~', '%2E', '%2A', ' ', '%2D');
            $parts[$key] = str_replace($search, $replace, rawurlencode($value));
        }
        return preg_replace("/(%[A-Za-z0-9]{2})/e", "strtolower('\\0')", implode('&', $parts));
    }

    /**
     * Generate signature for X-PAYPAL-AUTHORIZATION header
     *
     * @param array $params
     * @param string $consumerSecret
     * @param string $tokenSecret
     * @param string $url
     * @return string
     */
    protected function _prepareSignature($params, $consumerSecret, $tokenSecret, $url)
    {
        $data = $this->_prepareString(
            array('POST', $url, http_build_query($params))
        );

        $key = $this->_prepareString(
            array($consumerSecret, $tokenSecret)
        );

        return base64_encode(hash_hmac('sha1', $data, $key, true));
    }

    /**
     * Get Payer Id
     *
     * @param string $token
     * @param array $code
     * @return string
     */
    public function getPayerId($token, $code)
    {
        $personalData = $this->getBasicPersonalData($token, $code);
        return $this->_getPersonalDataFromResponse('payerId', $personalData);
    }

    /**
     * Find and return personal info from response by key
     *
     * @param string $key
     * @param array $response
     * @return string
     */
    protected function _getPersonalDataFromResponse($key, $response)
    {
        for ($i = 0; isset($response['response_personalData(' . $i . ')_personalDataKey']); $i++) {
            if ($response['response_personalData(' . $i . ')_personalDataKey'] == $this->_basicPersonalData[$key]) {
                return $response['response_personalData(' . $i . ')_personalDataValue'];
            }
        }

        return '';
    }

    /**
     * Prepare API call request
     *
     * @param array $request
     * @return array
     */
    protected function _prepareRequest($request)
    {
        $request['requestEnvelope.errorLanguage'] = self::REQUEST_ERROR_LANGUAGE;
        return $request;
    }

    /**
     * Prepare API call response
     *
     * @param array $response
     * @return array
     */
    protected function _prepareResponse($response)
    {
        $output = array();
        parse_str($response, $output);
        return $output;
    }

    /**
     * Do the API permissions call
     *
     * @throws Mage_Core_Exception
     * @throws Exception
     * @param string $operation
     * @param array $request
     * @param array $headers
     * @return array
     */
    public function call($operation, $request, $headers = array())
    {
        try {
            $debugData = array('operation' => $operation, 'request'  => $request);
            $http = new Varien_Http_Adapter_Curl();
            $config = array(
                'timeout'    => 30,
                'verifypeer' => $this->_config->verifyPeer,
                'header'  => false
            );

            $http->setConfig($config);

            $baseHeaders = array(
                'X-PAYPAL-SECURITY-USERID: ' . $this->getApiUsername(),
                'X-PAYPAL-SECURITY-PASSWORD: ' . $this->getApiPassword(),
                'X-PAYPAL-SECURITY-SIGNATURE: ' .  $this->getApiSignature(),
                'X-PAYPAL-REQUEST-DATA-FORMAT: NV',
                'X-PAYPAL-RESPONSE-DATA-FORMAT: NV',
                'X-PAYPAL-APPLICATION-ID: ' . $this->_config->applicationId
            );

            $headers = array_merge($baseHeaders, $headers);

            $http->write(
                Zend_Http_Client::POST,
                $this->getApiEndpoint($operation),
                '1.1',
                $headers,
                $this->_buildQuery($request)
            );

            $response = $http->read();

        } catch (Exception $e) {
            $debugData['http_error'] = array('error' => $e->getMessage(), 'code' => $e->getCode());
            $this->_debug($debugData);
            throw $e;
        }

        if ($http->getErrno()) {
            Mage::logException(
                new Exception(
                    sprintf('PayPal permissions connection error #%s: %s', $http->getErrno(), $http->getError())
                )
            );
            $http->close();

            $debugData['http_error'] = array('error' => $http->getError(), 'code' => $http->getErrno());
            $this->_debug($debugData);

            Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('Unable to communicate with the PayPal gateway.'));
        }
        $http->close();

        $response = $this->_prepareResponse($response);
        $debugData['response'] = $response;
        if (!$this->_checkCallResponse($response)) {
            $debugData['call_error'] = array('error' => 'Operation failed', 'response' => $response);
            $this->_debug($debugData);
            Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('PayPal permissions operation failed.'));
        }
        $debugData['response'] = $response;
        $this->_debug($debugData);

        return $response;
    }

    /**
     * Catch success calls and collect warnings
     *
     * @throws Mage_Core_Exception
     * @param array $response
     * @return bool
     */
    protected function _checkCallResponse($response)
    {
        if (!isset($response['responseEnvelope_ack'])) {
            Mage::throwException(Mage::helper('Mage_Paypal_Helper_Data')->__('Incorrect PayPal response'));
        }

        $ack = $response['responseEnvelope_ack'];
        if ($ack == 'Success' || $ack == 'SuccessWithWarning') {
            return true;
        }

        /*
         * Collect errors in case of Failure response
         */
        if ($ack == 'Failure') {
            $errorList = array();
            for ($i = 0; isset($response['error(' . $i . ')_severity']); $i++) {
                $errorList[] = sprintf('%s %s: %s',
                    isset($response['error(' . $i . ')_severity']) ? $response['error(' . $i . ')_severity'] : '',
                    isset($response['error(' . $i . ')_errorId']) ? $response['error(' . $i . ')_errorId'] : '',
                    isset($response['error(' . $i . ')_message']) ? $response['error(' . $i . ')_message'] : ''
                );
            }
            if ($errorList) {
                $debugData  = array();
                $debugData['response'] = $response;
                $this->_debug($debugData);
                Mage::throwException(implode(' ', $errorList));
            }
        }
        return false;
    }

    /**
     * API endpoint getter
     *
     * @param string $operation
     * @return string
     */
    public function getApiEndpoint($operation = '')
    {
        $url = 'https://svcs%s.paypal.com/Permissions/%s';
        return sprintf($url, $this->_config->sandboxFlag ? '.sandbox' : '', $operation);
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
                ->log($debugData);
        }
    }

}
