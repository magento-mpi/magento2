<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @category    Mage
 * @package     Mage_OAuth
 * @copyright   Copyright (c) 2010 Magento Inc. (http://www.magentocommerce.com)
 * @license     http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */

/**
 * oAuth Server
 *
 * @category    Mage
 * @package     Mage_OAuth
 * @author      Magento Core Team <core@magentocommerce.com>
 */
class Mage_OAuth_Model_Server
{
    /**#@+
     * OAuth result statuses
     */
    const ERR_OK                                = 0;
    const ERR_VERSION_REJECTED                  = 1;
    const ERR_PARAMETER_ABSENT                  = 2;
    const ERR_PARAMETER_REJECTED                = 3;
    const ERR_TIMESTAMP_REFUSED                 = 4;
    const ERR_NONCE_USED                        = 5;
    const ERR_SIGNATURE_METHOD_REJECTED         = 6;
    const ERR_SIGNATURE_INVALID                 = 7;
    const ERR_CONSUMER_KEY_UNKNOWN              = 8;
    const ERR_CONSUMER_KEY_REJECTED             = 9;
    const ERR_CONSUMER_KEY_REFUSED              = 10;
    const ERR_TOKEN_USED                        = 11;
    const ERR_TOKEN_EXPIRED                     = 12;
    const ERR_TOKEN_REVOKED                     = 13;
    const ERR_TOKEN_REJECTED                    = 14;
    const ERR_VERIFIER_INVALID                  = 15;
    const ERR_ADDITIONAL_AUTHORIZATION_REQUIRED = 16;
    const ERR_PERMISSION_UNKNOWN                = 17;
    const ERR_PERMISSION_DENIED                 = 18;
    const ERR_USER_REFUSED                      = 19;
    /**#@- */

    /**#@+
     * Signature Methods
     */
    const SIGNATURE_HMAC  = 'HMAC-SHA1';
    const SIGNATURE_RSA   = 'RSA-SHA1';
    const SIGNATURE_PLAIN = 'PLAINTEXT';
    /**#@- */

    /**#@+
     * Request Types
     */
    const REQUEST_INITIATE  = 'initiate';  // ask for temporary credentials
    const REQUEST_AUTHORIZE = 'authorize'; // display authorize form
    const REQUEST_TOKEN     = 'token';     // ask for permanent credentials
    const REQUEST_RESOURCE  = 'resource';  // ask for protected resource using permanent credentials
    /**#@- */

    /**#@+
     * HTTP Response Codes
     */
    const HTTP_OK           = 200;
    const HTTP_BAD_REQUEST  = 400;
    const HTTP_UNAUTHORIZED = 401;
    /**#@- */

    /**
     * Value of callback URL when it is established or if cliaent is unable to receive callbacks
     *
     * @link http://tools.ietf.org/html/rfc5849#section-2.1     Requirement in RFC-5849
     */
    const CALLBACK_ESTABLISHED = 'oob';

    /**
     * Consumer object
     *
     * @var Mage_OAuth_Model_Consumer
     */
    protected $_consumer;

    /**
     * Error code to error messages pairs
     *
     * @var array
     */
    protected $_errors = array(
        self::ERR_VERSION_REJECTED                  => 'version_rejected',
        self::ERR_PARAMETER_ABSENT                  => 'parameter_absent',
        self::ERR_PARAMETER_REJECTED                => 'parameter_rejected',
        self::ERR_TIMESTAMP_REFUSED                 => 'timestamp_refused',
        self::ERR_NONCE_USED                        => 'nonce_used',
        self::ERR_SIGNATURE_METHOD_REJECTED         => 'signature_method_rejected',
        self::ERR_SIGNATURE_INVALID                 => 'signature_invalid',
        self::ERR_CONSUMER_KEY_UNKNOWN              => 'consumer_key_unknown',
        self::ERR_CONSUMER_KEY_REJECTED             => 'consumer_key_rejected',
        self::ERR_CONSUMER_KEY_REFUSED              => 'consumer_key_refused',
        self::ERR_TOKEN_USED                        => 'token_used',
        self::ERR_TOKEN_EXPIRED                     => 'token_expired',
        self::ERR_TOKEN_REVOKED                     => 'token_revoked',
        self::ERR_TOKEN_REJECTED                    => 'token_rejected',
        self::ERR_VERIFIER_INVALID                  => 'verifier_invalid',
        self::ERR_ADDITIONAL_AUTHORIZATION_REQUIRED => 'additional_authorization_required',
        self::ERR_PERMISSION_UNKNOWN                => 'permission_unknown',
        self::ERR_PERMISSION_DENIED                 => 'permission_denied',
        self::ERR_USER_REFUSED                      => 'user_refused'
    );

    /**
     * oAuth helper object
     *
     * @var Mage_OAuth_Helper_Data
     */
    protected $_helper;

    /**
     * Request parameters
     *
     * @var array
     */
    protected $_params = null;

    /**
     * Request type: initiate, permanent token request or authorized one
     *
     * @var string
     */
    protected $_requestType;

    /**
     * Response object
     *
     * @var Mage_Core_Controller_Response_Http
     */
    protected $_response = null;

    /**
     * Token object
     *
     * @var Mage_OAuth_Model_Token
     */
    protected $_token;

    /**
     * Internal constructor not depended on params
     */
    public function __construct()
    {
        $this->_helper = Mage::helper('oauth');
    }

    /**
     * Retrieve parameters from request object
     *
     * @param Mage_Core_Controller_Request_Http $request
     * @return Mage_OAuth_Model_Server
     */
    protected function _fetchParams(Mage_Core_Controller_Request_Http $request)
    {
        $this->_params = $request->getQuery();

        if ($request->getHeader(Zend_Http_Client::CONTENT_TYPE) == Zend_Http_Client::ENC_URLENCODED) {
            $bodyParams = array();

            parse_str($request->getRawBody(), $bodyParams);

            if (count($bodyParams)) {
                $this->_params = array_merge($this->_params, $bodyParams);
            }
        }
        $headerValue = $request->getHeader('Authorization');

        if ($headerValue) {
            $headerValue = substr($headerValue, 6); // ignore 'OAuth ' at the beginning

            foreach (explode(',', $headerValue) as $paramStr) {
                $nameAndValue = explode('=', $paramStr, 2);

                if (count($nameAndValue) < 2) {
                    continue;
                }
                if (preg_match('/oauth_[a-z_-]+/', $nameAndValue[0])) {
                    $this->_params[rawurldecode($nameAndValue[0])] = rawurldecode(trim($nameAndValue[1], '"'));
                }
            }
        }
        return $this;
    }

    /**
     * Retrieve response object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    protected function _getResponse()
    {
        if (null === $this->_response) {
            $this->_response = Mage::app()->getResponse();
        }
        return $this->_response;
    }

    /**
     * Initialize consumer
     */
    protected function _initConsumer()
    {
        $this->_consumer = Mage::getModel('oauth/consumer');

        $this->_consumer->load($this->_params['oauth_consumer_key'], 'key');

        if (!$this->_consumer->getId()) {
            $this->_throwException('', self::ERR_CONSUMER_KEY_UNKNOWN);
        }
    }

    /**
     * Load token object, validate it, set access data and save
     *
     * @return Mage_OAuth_Model_Server
     */
    protected function _initToken()
    {
        $this->_token = Mage::getModel('oauth/token');

        // no additional actions required for initiate request
        if (self::REQUEST_INITIATE != $this->_requestType) {
            $this->_validateTokenParam();

            $this->_token->load($this->_params['oauth_token'], 'token');

            if (!$this->_token->getId()) {
                $this->_throwException($this->_params['oauth_token'], self::ERR_TOKEN_REJECTED);
            }
            if (self::REQUEST_TOKEN == $this->_requestType) {
                $this->_validateVerifierParam();

                if ($this->_token->getVerifier() != $this->_params['oauth_verifier']) {
                    $this->_throwException('', self::ERR_VERIFIER_INVALID);
                }
                if ($this->_token->getConsumerId() != $this->_consumer->getId()) {
                    $this->_throwException('', self::ERR_TOKEN_REJECTED);
                }
                if (Mage_OAuth_Model_Token::TYPE_REQUEST != $this->_token->getType()) {
                    $this->_throwException('', self::ERR_TOKEN_USED);
                }
            } elseif (self::REQUEST_AUTHORIZE == $this->_requestType) {
                if ($this->_token->getAuthorized()) {
                    $this->_throwException('', self::ERR_TOKEN_USED);
                }
            } elseif (self::REQUEST_RESOURCE == $this->_requestType) {
                if (Mage_OAuth_Model_Token::TYPE_ACCESS != $this->_token->getType()) {
                    $this->_throwException('', self::ERR_TOKEN_REJECTED);
                }
                if ($this->_token->getRevoked()) {
                    $this->_throwException('', self::ERR_TOKEN_REVOKED);
                }
                //TODO: Implement check for expiration (after it implemented in token model)
            }
        }
        return $this;
    }

    /**
     * Extract parameters from sources (GET, FormBody, Authorization header), decode them and validate
     *
     * @param Mage_Core_Controller_Request_Http $request Request object
     * @param string $requestType Request type - one of REQUEST_... class constant
     * @param string|null $url OPTIONAL Requested URL (used in signature verification)
     *                                  It will try to define it automatically if possible
     * @return Mage_OAuth_Model_Server
     */
    protected function _processRequest(Mage_Core_Controller_Request_Http $request, $requestType, $url = null)
    {
        // validate request type
        if (self::REQUEST_AUTHORIZE != $requestType
            && self::REQUEST_INITIATE != $requestType
            && self::REQUEST_RESOURCE != $requestType
            && self::REQUEST_TOKEN != $requestType) {
            Mage::throwException('Invalid request type');
        }
        $this->_requestType = $requestType;

        // get parameters from request
        $this->_fetchParams($request);

        // make generic validation of request parameters
        $this->_validateParams();

        // initialize consumer
        $this->_initConsumer();

        // initialize token
        $this->_initToken();

        // validate signature
        $this->_validateSignature($url);

        // save token if signature validation succeed
        $this->_saveToken();

        return $this;
    }

    /**
     * Report problem during request
     *
     * @param Mage_Oauth_Exception $e
     * @return string
     * @todo Move this method to try...catch without "exit"
     */
    protected function _reportProblem(Mage_Oauth_Exception $e)
    {
        $exceptionCode = $e->getCode();

        if (self::PARAMETER_ABSENT == $exceptionCode) {
            $msgAdd = '&oauth_parameters_absent=' . $e->getMessage();
        } elseif (self::SIGNATURE_INVALID == $exceptionCode) {
            $msgAdd =  '&debug_sbs=' . $e->getMessage();
        } else {
            $msgAdd = '';
        }
        if (isset($this->_errors[$exceptionCode])) {
            $msg = $this->_errors[$exceptionCode];
        } else {
            $msg = 'unknown_problem';
            $msgAdd = '&code=' . $exceptionCode;
        }
        if ($e->getMessage()) {
            $msgAdd .= '&message=' . $e->getMessage();
        }
        $this->_getResponse()->setBody('oauth_problem=' . $msg . $msgAdd);

        $this->_getResponse()->setHttpResponseCode(400); // BAD REQUEST
        $this->_getResponse()->sendResponse();
        exit;
    }

    /**
     * Save token
     */
    protected function _saveToken()
    {
        if (self::REQUEST_INITIATE == $this->_requestType) {
            if (empty($this->_params['oauth_callback'])) {
                $callbackUrl = $this->_consumer->getCallBackUrl();
            } else {
                $callbackUrl = $this->_params['oauth_callback'];
            }
            if (self::CALLBACK_ESTABLISHED != $callbackUrl && !Zend_Uri::check($callbackUrl)) {
                $this->_throwException('oauth_callback', self::ERR_PARAMETER_REJECTED);
            }
            $this->_token->createRequestToken($this->_consumer->getId(), $callbackUrl);
        } elseif (self::REQUEST_TOKEN == $this->_requestType) {
            $this->_token->convertToAccess();
        }
    }

    /**
     * Throw OAuth exception
     *
     * @param string $message Exception message
     * @param int $code Exception code
     * @throws Mage_OAuth_Exception
     */
    protected function _throwException($message = '', $code = 0)
    {
        throw Mage::exception('Mage_OAuth', $message, $code);
    }

    /**
     * Validate nonce request data
     *
     * @param string $nonce Nonce string
     * @param string|int $timestamp UNIX Timestamp
     */
    protected function _validateNonce($nonce, $timestamp)
    {
        $timestamp = (int) $timestamp;

        if ($timestamp <= 0 || $timestamp > time()) {
            $this->_throwException('', self::ERR_TIMESTAMP_REFUSED);
        }
        /** @var $nonceObj Mage_OAuth_Model_Nonce */
        $nonceObj = Mage::getModel('oauth/nonce');

        $nonceObj->load($nonce, 'nonce');

        if ($nonceObj->getTimestamp() == $timestamp) {
            $this->_throwException('', self::ERR_NONCE_USED);
        }
        $nonceObj->setNonce($nonce)
            ->setTimestamp($timestamp)
            ->save();
    }

    /**
     * Validate parameters
     */
    protected function _validateParams()
    {
        // validate version if specified
        if (isset($this->_params['oauth_version']) && '1.0' != $this->_params['oauth_version']) {
            $this->_throwException('', self::ERR_VERSION_REJECTED);
        }
        // required parameters validation
        $reqFields = array('oauth_consumer_key', 'oauth_signature_method', 'oauth_signature');

        foreach ($reqFields as $reqField) {
            if (empty($this->_params[$reqField])) {
                $this->_throwException($reqField, self::ERR_PARAMETER_ABSENT);
            }
        }
        // validate parameters type
        foreach ($this->_params as $paramName => $paramValue) {
            if (!is_string($paramValue)) {
                $this->_throwException($paramName, self::ERR_PARAMETER_REJECTED);
            }
        }
        // validate signature method
        if (!in_array($this->_params['oauth_signature_method'], $this->getValidSignatureMethods())) {
            $this->_throwException('', self::ERR_SIGNATURE_METHOD_REJECTED);
        }
        // validate nonce data if signature method is not PLAINTEXT
        if (self::SIGNATURE_PLAIN != $this->_params['oauth_signature_method']) {
            if (empty($this->_params['oauth_nonce'])) {
                $this->_throwException('oauth_nonce', self::ERR_PARAMETER_ABSENT);
            }
            if (empty($this->_params['oauth_timestamp'])) {
                $this->_throwException('oauth_timestamp', self::ERR_PARAMETER_ABSENT);
            }
            $this->_validateNonce($this->_params['oauth_nonce'], $this->_params['oauth_timestamp']);
        }
    }

    /**
     * Validate signature
     *
     * @param string $url OPTIONAL Request URL to be a part of data to sign (if not specified - find by request type)
     */
    protected function _validateSignature($url = null)
    {
        if (null === $url) {
            if (self::REQUEST_INITIATE == $this->_requestType) {
                $url = $this->_helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_INITIATE);
            } elseif (self::REQUEST_TOKEN == $this->_requestType) {
                $url = $this->_helper->getProtocolEndpointUrl(Mage_OAuth_Helper_Data::ENDPOINT_TOKEN);
            } else {
                Mage::throwException('Can not automatically find URL for current type of request');
            }
        }
        $util = new Zend_Oauth_Http_Utility();

        $calculatedSign = $util->sign(
            $this->_params,
            $this->_params['oauth_signature_method'],
            $this->_consumer->getSecret(),
            $this->_token->getSecret(),
            Zend_Oauth::POST,
            $url
        );

        if ($calculatedSign != $this->_params['oauth_signature']) {
            $this->_throwException($calculatedSign, self::ERR_SIGNATURE_INVALID);
        }
    }

    /**
     * Check for 'oauth_token' parameter
     */
    protected function _validateTokenParam()
    {
        if (empty($this->_params['oauth_token'])) {
            $this->_throwException('oauth_token', self::ERR_PARAMETER_ABSENT);
        }
        if (!is_string($this->_params['oauth_token'])) {
            $this->_throwException('', self::ERR_TOKEN_REJECTED);
        }
    }

    /**
     * Check for 'oauth_verifier' parameter
     */
    protected function _validateVerifierParam()
    {
        if (empty($this->_params['oauth_verifier'])) {
            $this->_throwException('oauth_verifier', self::ERR_PARAMETER_ABSENT);
        }
        if (!is_string($this->_params['oauth_verifier'])) {
            $this->_throwException('', self::ERR_VERIFIER_INVALID);
        }
    }

    /**
     * Process request for permanent access token
     *
     * @param Mage_Core_Controller_Request_Http $request OPTIONAL Request object
     */
    public function accessToken(Mage_Core_Controller_Request_Http $request = null)
    {
        $this->_processRequest(null === $request ? Mage::app()->getRequest() : $request, self::REQUEST_TOKEN);

        $this->_getResponse()->setBody($this->_token->toString());
    }

    /**
     * Validate request, authorize token and return it
     *
     * @param Mage_Core_Controller_Request_Http|null $request
     * @return Mage_OAuth_Model_Token
     */
    public function authorizeToken(Mage_Core_Controller_Request_Http $request = null)
    {
        $this->_requestType = self::REQUEST_AUTHORIZE;

        $this->_fetchParams(null === $request ? Mage::app()->getRequest() : $request);
        $this->_initToken();

        $this->_token->authorize();

        return $this->_token;
    }

    /**
     * Validate request with access token
     *
     * @param Mage_Core_Controller_Request_Http|null $request
     * @param string $url OPTIONAL Request URL
     */
    public function checkAccessRequest(Mage_Core_Controller_Request_Http $request = null, $url = null)
    {
        $this->_processRequest(null === $request ? Mage::app()->getRequest() : $request, self::REQUEST_RESOURCE, $url);
    }

    /**
     * Process authorize request
     *
     * @param Mage_Core_Controller_Request_Http $request OPTIONAL Request object
     */
    public function checkAuthorizeRequest(Mage_Core_Controller_Request_Http $request = null)
    {
        $this->_requestType = self::REQUEST_AUTHORIZE;

        $request = null === $request ? Mage::app()->getRequest() : $request;

        if (!$request->isGet()) {
            Mage::throwException('Request is not GET');
        }
        $this->_fetchParams($request);
        $this->_initToken();
    }

    /**
     * Return complete callback URL or boolean FALSE if no callback provided
     *
     * @param Mage_OAuth_Model_Token $token Ещлут щиоусе
     * @return bool|string
     */
    public function getFullCallbackUrl(Mage_OAuth_Model_Token $token)
    {
        if (!$token->getAuthorized()) {
            Mage::throwException('Token is not authorized');
        }
        $callbackUrl = $token->getCallbackUrl();

        if (self::CALLBACK_ESTABLISHED == $callbackUrl) {
            return false;
        }
        $callbackUrl .= (false === strpos($callbackUrl, '?') ? '?' : '&');

        return $callbackUrl . 'oauth_token=' . $token->getToken() . '&oauth_verifier=' . $token->getVerifier();
    }

    /**
     * Retrieve array of valid signature methods
     *
     * @return array
     */
    public function getValidSignatureMethods()
    {
        return array(self::SIGNATURE_RSA, self::SIGNATURE_HMAC, self::SIGNATURE_PLAIN);
    }

    /**
     * Process request for temporary (initiative) token
     *
     * @param Mage_Core_Controller_Request_Http $request OPTIONAL Request object
     */
    public function initiateToken(Mage_Core_Controller_Request_Http $request = null)
    {
        $this->_processRequest(null === $request ? Mage::app()->getRequest() : $request, self::REQUEST_INITIATE);

        $this->_getResponse()->setBody($this->_token->toString() . '&oauth_callback_confirmed=true');
    }

    /**
     * Set response object
     *
     * @param Mage_Core_Controller_Response_Http $response
     * @return Mage_OAuth_Model_Server
     */
    public function setResponse(Mage_Core_Controller_Response_Http $response)
    {
        $this->_response = $response;

        $this->_response->setHeader(Zend_Http_Client::CONTENT_TYPE, Zend_Http_Client::ENC_URLENCODED, true);
        $this->_response->setHttpResponseCode(self::HTTP_OK);

        return $this;
    }
}
