<?php
/**
 * {license_notice}
 *
 * @copyright  {copyright}
 * @license    {license_link}
 */

/**
 * OAuth View Helper for Controllers
 */
class Magento_Oauth_Helper_Data extends Magento_Core_Helper_Abstract
{

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK = 200;
    const HTTP_BAD_REQUEST = 400;
    const HTTP_UNAUTHORIZED = 401;
    const HTTP_METHOD_NOT_ALLOWED = 405;
    const HTTP_INTERNAL_ERROR = 500;
    /**#@-*/

    /**
     * Error code to error messages pairs
     *
     * @var array
     */
    protected $_errors = array(
        Magento_Oauth_Helper_Service::ERR_VERSION_REJECTED => 'version_rejected',
        Magento_Oauth_Helper_Service::ERR_PARAMETER_ABSENT => 'parameter_absent',
        Magento_Oauth_Helper_Service::ERR_PARAMETER_REJECTED => 'parameter_rejected',
        Magento_Oauth_Helper_Service::ERR_TIMESTAMP_REFUSED => 'timestamp_refused',
        Magento_Oauth_Helper_Service::ERR_NONCE_USED => 'nonce_used',
        Magento_Oauth_Helper_Service::ERR_SIGNATURE_METHOD_REJECTED => 'signature_method_rejected',
        Magento_Oauth_Helper_Service::ERR_SIGNATURE_INVALID => 'signature_invalid',
        Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_REJECTED => 'consumer_key_rejected',
        Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_INVALID => 'consumer_key_invalid',
        Magento_Oauth_Helper_Service::ERR_TOKEN_USED => 'token_used',
        Magento_Oauth_Helper_Service::ERR_TOKEN_EXPIRED => 'token_expired',
        Magento_Oauth_Helper_Service::ERR_TOKEN_REVOKED => 'token_revoked',
        Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED => 'token_rejected',
        Magento_Oauth_Helper_Service::ERR_VERIFIER_INVALID => 'verifier_invalid',
        Magento_Oauth_Helper_Service::ERR_PERMISSION_UNKNOWN => 'permission_unknown',
        Magento_Oauth_Helper_Service::ERR_PERMISSION_DENIED => 'permission_denied',
        Magento_Oauth_Helper_Service::ERR_METHOD_NOT_ALLOWED => 'method_not_allowed'
    );

    /**
     * Error code to HTTP error code
     *
     * @var array
     */
    protected $_errorsToHttpCode = array(
        Magento_Oauth_Helper_Service::ERR_VERSION_REJECTED => self::HTTP_BAD_REQUEST,
        Magento_Oauth_Helper_Service::ERR_PARAMETER_ABSENT => self::HTTP_BAD_REQUEST,
        Magento_Oauth_Helper_Service::ERR_PARAMETER_REJECTED => self::HTTP_BAD_REQUEST,
        Magento_Oauth_Helper_Service::ERR_TIMESTAMP_REFUSED => self::HTTP_BAD_REQUEST,
        Magento_Oauth_Helper_Service::ERR_NONCE_USED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_SIGNATURE_METHOD_REJECTED => self::HTTP_BAD_REQUEST,
        Magento_Oauth_Helper_Service::ERR_SIGNATURE_INVALID => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_REJECTED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_CONSUMER_KEY_INVALID => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_TOKEN_USED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_TOKEN_EXPIRED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_TOKEN_REVOKED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_TOKEN_REJECTED => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_VERIFIER_INVALID => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_PERMISSION_UNKNOWN => self::HTTP_UNAUTHORIZED,
        Magento_Oauth_Helper_Service::ERR_PERMISSION_DENIED => self::HTTP_UNAUTHORIZED
    );


    /**
     * @param Magento_Core_Helper_Context $context
     */
    public function __construct(
        Magento_Core_Helper_Context $context
    ) {
        parent::__construct($context);
    }


    /**
     * Process HTTP request object and prepare for token validation
     *
     * @param Zend_Controller_Request_Http $httpRequest
     * @param array $bodyParams array of key value body parameters
     * @return array
     */
    public function prepareServiceRequest($httpRequest, $bodyParams = array())
    {
        //TODO: Fix needed for $this->getRequest()->getHttpHost(). Hosts with port are not covered
        $requestUrl = $httpRequest->getScheme() . '://' . $httpRequest->getHttpHost() .
            $httpRequest->getRequestUri();

        $serviceRequest = array();
        $serviceRequest['request_url'] = $requestUrl;
        $serviceRequest['http_method'] = $httpRequest->getMethod();

        $oauthParams = $this->_processRequest($httpRequest->getHeader('Authorization'),
                                              $httpRequest->getHeader(Zend_Http_Client::CONTENT_TYPE),
                                              $httpRequest->getRawBody(),
                                              $requestUrl);
        //Use body parameters only for POST and PUT
        $bodyParams = is_array($bodyParams) && ($httpRequest->getMethod() == 'POST' ||
            $httpRequest->getMethod() == 'PUT') ? $bodyParams : array();
        return array_merge($serviceRequest, $oauthParams, $bodyParams);
    }

    /**
     * Process oauth related protocol information and return as an array
     *
     * @param $authHeaderValue
     * @param $contentTypeHeader
     * @param $requestBodyString
     * @param $requestUrl
     * @return array
     * merged array of oauth protocols and request parameters. eg :
     * <pre>
     * array (
     *         'oauth_version' => '1.0',
     *         'oauth_signature_method' => 'HMAC-SHA1',
     *         'oauth_nonce' => 'rI7PSWxTZRHWU3R',
     *         'oauth_timestamp' => '1377183099',
     *         'oauth_consumer_key' => 'a6aa81cc3e65e2960a4879392445e718',
     *         'oauth_signature' => 'VNg4mhFlXk7%2FvsxMqqUd5DWIj9s%3D'',
     *         'request_url' => 'http://magento.ll/oauth/token/access',
     *         'http_method' => 'POST'
     * )
     * </pre>
     */
    protected function _processRequest($authHeaderValue, $contentTypeHeader, $requestBodyString, $requestUrl)
    {
        $protocolParams = array();

        if ($authHeaderValue && 'oauth' === strtolower(substr($authHeaderValue, 0, 5))) {
            $authHeaderValue = substr($authHeaderValue, 6); // ignore 'OAuth ' at the beginning

            foreach (explode(',', $authHeaderValue) as $paramStr) {
                $nameAndValue = explode('=', trim($paramStr), 2);

                if (count($nameAndValue) < 2) {
                    continue;
                }
                if ($this->_isProtocolParameter($nameAndValue[0])) {
                    $protocolParams[rawurldecode($nameAndValue[0])] = rawurldecode(trim($nameAndValue[1], '"'));
                }
            }
        }

        if ($contentTypeHeader && 0 === strpos($contentTypeHeader, Zend_Http_Client::ENC_URLENCODED)) {
            $protocolParamsNotSet = !$protocolParams;

            parse_str($requestBodyString, $protocolBodyParams);

            foreach ($protocolBodyParams as $bodyParamName => $bodyParamValue) {
                if (!$this->_isProtocolParameter($bodyParamName)) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                } elseif ($protocolParamsNotSet) {
                    $protocolParams[$bodyParamName] = $bodyParamValue;
                }
            }
        }
        $protocolParamsNotSet = !$protocolParams;

        if (($queryString = Zend_Uri_Http::fromString($requestUrl)->getQuery())) {
            foreach (explode('&', $queryString) as $paramToValue) {
                $paramData = explode('=', $paramToValue);

                if (2 === count($paramData) && !$this->_isProtocolParameter($paramData[0])) {
                    $protocolParams[rawurldecode($paramData[0])] = rawurldecode($paramData[1]);
                }
            }
        }
        if ($protocolParamsNotSet) {
            $this->_fetchProtocolParamsFromQuery($protocolParams, $queryString);
        }

        // Combine request and header parameters
        return $protocolParams;
    }

    /**
     * Create response string for problem during request and set HTTP error code
     *
     * @param Exception $exception
     * @param Zend_Controller_Response_Http $response OPTIONAL If NULL - will use internal getter
     * @return string
     */
    public function prepareErrorResponse(
        Exception $exception,
        Zend_Controller_Response_Http $response = null
    ) {
        $errorMap = $this->_errors;
        $errorsToHttpCode = $this->_errorsToHttpCode;

        $eMsg = $exception->getMessage();

        if ($exception instanceof Magento_Oauth_Exception) {
            $eCode = $exception->getCode();

            if (isset($errorMap[$eCode])) {
                $errorMsg = $errorMap[$eCode];
                $responseCode = $errorsToHttpCode[$eCode];
            } else {
                $errorMsg = 'unknown_problem&code=' . $eCode;
                $responseCode = self::HTTP_INTERNAL_ERROR;
            }
            if (Magento_Oauth_Helper_Service::ERR_PARAMETER_ABSENT == $eCode) {
                $errorMsg .= '&oauth_parameters_absent=' . $eMsg;
            } elseif ($eMsg) {
                $errorMsg .= '&message=' . $eMsg;
            }
        } else {
            $errorMsg = 'internal_error&message=' . ($eMsg ? $eMsg : 'empty_message');
            $responseCode = self::HTTP_INTERNAL_ERROR;
        }

        $response->setHttpResponseCode($responseCode);
        return array('oauth_problem' => $errorMsg);
    }

    /**
     * Retrieve protocol parameters from query string
     *
     * @param $protocolParams
     * @param $queryString
     */
    protected function _fetchProtocolParamsFromQuery(&$protocolParams, $queryString)
    {
        foreach ($queryString as $queryParamName => $queryParamValue) {
            if ($this->_isProtocolParameter($queryParamName)) {
                $protocolParams[$queryParamName] = $queryParamValue;
            }
        }
    }

    /**
     * Check if attribute is oAuth related
     *
     * @param string $attrName
     * @return bool
     */
    protected function _isProtocolParameter($attrName)
    {
        return (bool)preg_match('/oauth_[a-z_-]+/', $attrName);
    }
}
